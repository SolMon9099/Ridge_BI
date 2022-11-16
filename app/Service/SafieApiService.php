<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;
use KubAT\PhpSimple\HtmlDomParser;
use App\Models\Token;
use App\Models\MediaRequestHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SafieApiService
{
    private $api_url = 'https://openapi.safie.link/v1/';
    private $redirect_uri = 'http://54.95.92.211/';
    private $client_id = 'dc2537ffb887';
    private $secret = 'd25100e130499d0fb257df19cd5b0279';
    public $device_id = 'FvY6rnGWP12obPgFUj0a';
    private $safie_user_name = 'soumu@ridge-i.com';
    private $safie_password = 'ridge-i438';
    public $access_token = '';
    public $refresh_token = '';
    private $contract_no = null;
    private static $_instance = null;

    public function __construct($contract_no = null)
    {
        $main_user = AccountService::getAdminUserByContract($contract_no);
        if ($main_user != null) {
            if (isset($main_user->safie_user_name)) {
                $this->safie_user_name = $main_user->safie_user_name;
            }
            if (isset($main_user->safie_password)) {
                $this->safie_password = $main_user->safie_password;
            }
            if (isset($main_user->safie_client_id)) {
                $this->client_id = $main_user->safie_client_id;
            }
            if (isset($main_user->safie_client_secret)) {
                $this->secret = $main_user->safie_client_secret;
            }
            $this->contract_no = $main_user->contract_no;
        } else {
            $this->contract_no = 'super_admin';
        }
        $token_data = Token::query()->where('contract_no', $this->contract_no)->get()->first();

        if ($token_data != null) {
            $this->access_token = $token_data->access_token;
            $this->refresh_token = $token_data->refresh_token;
            $this->contract_no = $token_data->contract_no;
        } else {
            $this->generateToken();
        }
    }

    public static function getInstance($options = null)
    {
        if (SafieApiService::$_instance === null) {
            SafieApiService::$_instance = new SafieApiService($options);
        }

        return SafieApiService::$_instance;
    }

    public function generateRefreshToken()
    {
        if (isset($this->refresh_token) && $this->refresh_token != '') {
            $this->refreshToken($this->refresh_token);
        } else {
            $this->generateToken();
        }
    }

    public function generateToken()
    {
        Log::info('Start Generate Token-----------');
        $auth_code = $this->getAuthCode();
        $this->getAccessToken($auth_code);
    }

    public function updateTokenDB($data)
    {
        if (isset($data['token_type']) && $data['token_type'] == 'Bearer') {
            $token_record = Token::query()->where('contract_no', $this->contract_no)->get()->first();
            if ($token_record == null) {
                $token_record = new Token();
            }

            if (isset($data['access_token'])) {
                $token_record->access_token = $data['access_token'];
                $this->access_token = $data['access_token'];
            }
            if (isset($data['refresh_token'])) {
                $token_record->refresh_token = $data['refresh_token'];
                $this->refresh_token = $data['refresh_token'];
            }
            $token_record->contract_no = $this->contract_no;
            $token_record->save();
        }
    }

    public function getAccessToken($code)
    {
        $url = sprintf('%s%s', $this->api_url, 'auth/token');
        $params = [];
        $params['client_id'] = $this->client_id;
        $params['client_secret'] = $this->secret;
        $params['grant_type'] = 'authorization_code';
        $params['redirect_uri'] = env('SAFIE_REDIRECT_URL', $this->redirect_uri);
        $params['code'] = $code;    //認可コード
        $response = $this->sendPostwithHttpCode($url, null, $params);
        if (isset($response['http_code'])) {
            if ($response['http_code'] == 200) {
                $this->updateTokenDB($response['response_data']);
                Log::info('Success---- Generate Token');
            } else {
                Log::info('Error---- Generate Token : '.$response['http_code']);
            }
        }
    }

    public function refreshToken($refresh_token)
    {
        Log::info('Refresh = '.$refresh_token);
        $url = sprintf('%s%s', $this->api_url, 'auth/refresh-token');
        $params['client_id'] = $this->client_id;
        $params['client_secret'] = $this->secret;
        $params['grant_type'] = 'refresh_token';
        $params['refresh_token'] = $refresh_token;
        $params['scope'] = 'safie-api';
        $response = $this->sendPostwithHttpCode($url, null, $params);
        Log::info($response);
        if (isset($response['http_code'])) {
            if ($response['http_code'] == 200) {
                $this->updateTokenDB($response['response_data']);
                Log::info('Success ----- Finish Refresh Token');
            } else {
                Log::info('Not Success -> Regenerate Refresh Token : '.$response['http_code']);
                $this->generateToken();
            }
        }
    }

    public function getAuthUrl()
    {
        $url = sprintf('%s%s', $this->api_url, 'auth/authorize');

        $params = [];
        $params['client_id'] = $this->client_id;
        $params['response_type'] = 'code';
        $params['scope'] = 'safie-api';
        $params['redirect_uri'] = env('SAFIE_REDIRECT_URL', $this->redirect_uri);
        $params['state'] = '';

        $url = sprintf('%s?%s', $url, http_build_query($params));

        return $url;
    }

    public function getAuthCode()
    {
        Log::info('getAuthCode start------------');
        $auth_url = $this->getAuthUrl();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $auth_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('Auth httpcode='.$httpcode);
        curl_close($curl);
        $dom = HtmlDomParser::str_get_html($response);
        $code = '';
        $access_confirm = $dom->find('input[name="access_confirm"]');
        if (count($access_confirm) > 0) {
            $access_confirm = $access_confirm[0]->value;
            Log::info('access_confirm----------'.$access_confirm);
            $authorize_url = 'https://app.safie.link/auth/authorize';

            $params = [];
            $params['username'] = $this->safie_user_name;
            $params['password'] = $this->safie_password;
            $params['client_id'] = $this->client_id;
            $params['response_type'] = 'code';
            $params['scope'] = 'safie-api';
            $params['redirect_uri'] = env('SAFIE_REDIRECT_URL', $this->redirect_uri);
            $params['state'] = '';
            $params['access_confirm'] = $access_confirm;
            $params['authorize'] = '許可する';

            $curl = curl_init($authorize_url);
            //POSTで送信
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            Log::info('httpcode='.$httpcode);
            $split_data = explode('code=', $response);
            if (count($split_data) > 1) {
                $code = explode('&', $split_data[1])[0];
            }
            curl_close($curl);
        }

        return $code;
    }

    //画像取得
    public function getDeviceImage($device_id = null, $timestamp = null)
    {
        $device_id = $device_id != null ? $device_id : $this->device_id;
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/image', $device_id);

        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];
        if ($timestamp != null) {
            $param['timestamp'] = $timestamp;
            $url .= '?'.http_build_query($param);
        }
        $curl = curl_init($url);
        Log::debug("--- {$url} ---");
        Log::debug('access_token = '.$this->access_token);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, false);
        $ch = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode='.$httpcode);

        if ($httpcode == 401) {
            $this->generateRefreshToken();
        }

        if (curl_errno($curl)) {
            Log::debug('--- Curl エラー ---');
            echo curl_error($curl);
            curl_close($curl);
            if ($timestamp == null) {
                Storage::disk('recent_camera_image')->delete($device_id.'.jpeg');
            }

            return null;
        }

        if ($httpcode == 200) {
            $response = curl_multi_getcontent($curl);
            if ($timestamp == null) {
                Storage::disk('recent_camera_image')->put($device_id.'.jpeg', $response);
            }
            curl_close($curl);

            return $response;
        } else {
            curl_close($curl);
            if ($timestamp == null) {
                Storage::disk('recent_camera_image')->delete($device_id.'.jpeg');
            }

            return null;
        }
    }

    // HTTP Live Streamingプレイリスト取得
    public function getDeviceLiveStreamingList($device_id = null)
    {
        $device_id = $device_id != null ? $device_id : $this->device_id;
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/live/playlist.m3u8', $device_id);

        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];
        $curl = curl_init($url);
        Log::debug("--- {$url} ---");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, false);
        $ch = curl_exec($curl);
        if (curl_errno($curl)) {
            Log::debug('--- Curl エラー ---');
            echo curl_error($curl);
            curl_close($curl);

            return null;
        }
        $response = curl_multi_getcontent($curl);
        curl_close($curl);

        return $response;
    }

    //メディアファイル 作成要求一覧取得
    public function getMediaFileList($device_id = null)
    {
        $device_id = $device_id != null ? $device_id : $this->device_id;
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/media_files/requests', $device_id);
        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];
        $response = $this->sendGetApi($url, $header);

        return $response;
    }

    //メディアファイル 作成要求
    public function makeMediaFile($device_id = null, $start = null, $end = null, $request_resource = null)
    {
        $device_id = $device_id != null ? $device_id : $this->device_id;
        $media_history_data = MediaRequestHistory::query()->where('device_id', $device_id)
            ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-12 hour')))
            ->orderByDesc('created_at')->limit(50)
            ->get()->all();
        $check_camera_enable = true;
        if (count($media_history_data) >= 50) {
            $check_camera_enable = false;
            foreach ($media_history_data as $item) {
                if ($item->http_code == 200) {
                    $check_camera_enable = true;
                    break;
                }
            }
        }

        if ($check_camera_enable == false) {
            Log::info('動画取得中止 '.$device_id);
            DB::table('cameras')->where('camera_id', $device_id)->whereNull('deleted_at')->update(['is_enabled' => 0]);

            return null;
        }

        $url = sprintf('https://openapi.safie.link/v1/devices/%s/media_files/requests', $device_id);
        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];
        $params['start'] = $start;
        $params['end'] = $end;
        $response = $this->sendPostwithHttpCode($url, $header, $params, 'json');
        $res = null;

        if (isset($response['http_code'])) {
            if ($response['http_code'] == 200) {
                if (isset($response['response_data']) && isset($response['response_data']['request_id'])) {
                    $res = $response['response_data']['request_id'];
                }
            }
            $model = new MediaRequestHistory();
            $model->device_id = $device_id;
            $model->request_resource = $request_resource;
            $model->http_code = $response['http_code'];
            $model->start_time = date('Y-m-d H:i:s', strtotime($start));
            $model->end_time = date('Y-m-d H:i:s', strtotime($end));
            $model->time_diff = (int) ((strtotime($end) - strtotime($start)) / 60);
            $model->request_id = $res;
            if ($response['http_code'] == 200) {
                $model->status = 1;
            }
            $model->save();
            if ($response['http_code'] != 200) {
                return 'http_code_'.$response['http_code'];
            }
        }

        return $res;
    }

    //メディアファイル 作成要求取得
    public function getMediaFileStatus($device_id = null, $request_id = null)
    {
        $device_id = $device_id != null ? $device_id : $this->device_id;
        if ($request_id == null) {
            return null;
        }
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/media_files/requests/%s', $device_id, $request_id);
        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];

        $response = $this->sendGetApi($url, $header);

        return $response;
    }

    // メディアファイル 作成要求削除
    public function deleteMediaFile($device_id = null, $request_id = null)
    {
        $device_id = $device_id != null ? $device_id : $this->device_id;
        if ($request_id == null) {
            return null;
        }
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/media_files/requests/%s', $device_id, $request_id);
        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];
        $response = $this->sendDeleteApi($url, $header);
        if ($response == 200 || $response == 404) {
            MediaRequestHistory::query()->where('device_id', $device_id)->where('request_id', $request_id)
                ->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        return $response;
    }

    // メディアファイル ダウンロード
    public function downloadMediaFile($url)
    {
        ini_set('memory_limit', '1024M');
        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];

        $response = $this->sendGetApi($url, $header, false);

        return $response;
    }

    //デバイス一覧取得
    public function getAllDevices()
    {
        $url = sprintf('https://openapi.safie.link/v1/devices');
        $header = [
            'Authorization: Bearer '.$this->access_token,
            'Content-Type: application/json',
        ];
        $response = $this->sendGetApi($url, $header);

        return $response;
    }

    public function sendPostwithHttpCode($url, $header = null, $data = null, $request_type = 'query')
    {
        Log::info('【Start Post Api】url:'.$url);

        $curl = curl_init($url);
        //POSTで送信
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($data) {
            switch ($request_type) {
                case 'query':
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    break;
                case 'json':
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
            }
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode = '.$httpcode);

        if ($httpcode == 401) {
            $this->generateRefreshToken();
        }

        curl_close($curl);
        Log::info('【Finish Post Api】url:'.$url);
        if ($httpcode == 200) {
            $response_edit = strstr($response, '{');
            $response_return = json_decode($response_edit, true);

            return ['response_data' => $response_return, 'http_code' => $httpcode];
        } else {
            return ['response_data' => null, 'http_code' => $httpcode];
        }
    }

    public function sendPostApi($url, $header = null, $data = null, $request_type = 'query')
    {
        Log::info('【Start Post Api】url:'.$url);

        $curl = curl_init($url);
        //POSTで送信
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($data) {
            switch ($request_type) {
                case 'query':
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    break;
                case 'json':
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
            }
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode = '.$httpcode);
        if ($httpcode == 401) {
            $this->generateRefreshToken();
        }
        curl_close($curl);
        Log::info('【Finish Post Api】url:'.$url);
        if ($httpcode == 200) {
            $response_edit = strstr($response, '{');
            $response_return = json_decode($response_edit, true);

            return $response_return;
        } else {
            return null;
        }
    }

    public function sendDeleteApi($url, $header = null, $data = null)
    {
        Log::info('【Start Delete Api】url:'.$url);

        $curl = curl_init($url);
        //POSTで送信
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        }

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode = '.$httpcode);
        if ($httpcode == 401) {
            $this->generateRefreshToken();
        }
        Log::info('【Finish Delete Api】url:'.$url);
        curl_close($curl);

        return $httpcode;
    }

    public function sendGetApi($url, $header, $json_type = true)
    {
        Log::info('【Start Get Api】url:'.$url);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode = '.$httpcode);
        curl_close($curl);
        if ($httpcode == 401) {
            $this->generateRefreshToken();
        }

        if ($httpcode == 200) {
            if ($json_type == true) {
                $response_edit = strstr($response, '{');
                $response_return = json_decode($response_edit, true);

                return $response_return;
            } else {
                return $response;
            }
        } else {
            echo 'HTTP code: '.$httpcode;
            if ($httpcode == 404) {
                return 'not_found';
            } else {
                return null;
            }
        }
    }
}
