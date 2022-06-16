<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;
use KubAT\PhpSimple\HtmlDomParser;
use App\Models\Token;

class SafieApiService
{
    private $client_id = 'dc2537ffb887';
    private $secret = 'd25100e130499d0fb257df19cd5b0279';

    private $api_url = 'https://openapi.safie.link/v1/';
    private $redirect_uri = 'http://54.95.92.211/';
    // private $redirect_uri = 'http://120.143.15.36/';
    private $device_id = 'FvY6rnGWP12obPgFUj0a';
    private $auth_authorize = 'auth/authorize';
    private $auth_token = 'auth/token';

    private $refresh_url = 'auth/refresh-token';

    private $safie_user_name = 'soumu@ridge-i.com';
    private $safie_password = 'ridge-i438';

    private $access_token = '';
    private $refresh_token = '';

    public function __construct()
    {
        $token_data = Token::all();
        if (count($token_data) > 0) {
            $this->access_token = $token_data[0]->access_token;
            $this->refresh_token = $token_data[0]->refresh_token;
        } else {
            $this->generateToken();
        }
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
        $auth_code = $this->getAuthCode();
        $this->getAccessToken($auth_code);
    }

    public function updateTokenDB($data)
    {
        if (isset($data['token_type']) && $data['token_type'] == 'Bearer') {
            $token_data = Token::all();
            if (count($token_data) > 0) {
                $token_record = $token_data[0];
            } else {
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
            $token_record->save();
        }
    }

    public function getAccessToken($code)
    {
        $url = sprintf('%s%s', $this->api_url, $this->auth_token);  //https://openapi.safie.link/v1/auth/token
        $params = [];
        $params['client_id'] = env('CLIENT_ID', $this->client_id);   //'dc2537ffb887'
        $params['client_secret'] = env('SECRET', $this->secret);   //'d25100e130499d0fb257df19cd5b0279'
        $params['grant_type'] = 'authorization_code';
        $params['redirect_uri'] = env('SAFIE_REDIRECT_URL', $this->redirect_uri);
        $params['code'] = $code;    //認可コード
        $response = $this->sendPostApi($url, null, $params, false);
        $this->updateTokenDB($response);
    }

    public function refreshToken($refresh_token)
    {
        $url = sprintf('%s%s', $this->api_url, $this->refresh_url);
        $params['client_id'] = env('CLIENT_ID', $this->client_id);   //'dc2537ffb887'
        $params['client_secret'] = env('SECRET', $this->secret);   //'d25100e130499d0fb257df19cd5b0279'
        $params['grant_type'] = 'refresh_token';
        $params['refresh_token'] = $refresh_token;
        $params['scope'] = 'safie-api';
        $response = $this->sendPostApi($url, null, $params, false);
        $this->updateTokenDB($response);
    }

    public function getAuthUrl()
    {
        $url = sprintf('%s%s', $this->api_url, $this->auth_authorize);

        $params = [];
        $params['client_id'] = env('CLIENT_ID', $this->client_id);
        $params['response_type'] = 'code';
        $params['scope'] = 'safie-api';
        $params['redirect_uri'] = env('SAFIE_REDIRECT_URL', $this->redirect_uri);
        $params['state'] = '';

        $url = sprintf('%s?%s', $url, http_build_query($params));

        return $url;
    }

    public function getAuthCode()
    {
        $auth_url = $this->getAuthUrl();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $auth_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);
        $dom = HtmlDomParser::str_get_html($response);
        $code = '';
        $access_confirm = $dom->find('input[name="access_confirm"]');
        if (count($access_confirm) > 0) {
            $access_confirm = $access_confirm[0]->value;

            $authorize_url = 'https://app.safie.link/auth/authorize';

            $params = [];
            $params['username'] = env('SAFIE_USER_NAME', $this->safie_user_name);
            $params['password'] = env('SAFIE_PASSWORD', $this->safie_password);
            $params['client_id'] = env('CLIENT_ID', $this->client_id);
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
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            $response = curl_exec($curl);
            $split_data = explode('code=', $response);
            if (count($split_data) > 1) {
                $code = explode('&', $split_data[1])[0];
            }
        }

        return $code;
    }

    public function getDeviceImage()
    {
        $device_id = $this->device_id;
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/image', $device_id);

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

            return null;
        }
        $response = curl_multi_getcontent($curl);

        return $response;
    }

    /**
     * Api 送信関数.
     */
    public function callApi($url)
    {
        $curl = curl_init($url);
        Log::debug("--- {$url} ---");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, false);
        $ch = curl_exec($curl);
        if (curl_errno($curl)) {
            Log::debug('--- Curl エラー ---');
            echo curl_error($curl);

            return null;
        }
        $response = curl_multi_getcontent($curl);
        curl_close($curl);
        $ret = json_decode($ch);

        return $ret;
    }

    public function sendPostApi($url, $header = null, $data = null, $xform = false)
    {
        Log::info('【Start Post Api】url:'.$url);
        Log::info($data);

        $curl = curl_init($url);
        //POSTで送信
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        if ($xform) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        // $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        // echo 'HTTP code: '.$httpcode;

        $response_edit = strstr($response, '{');
        $response_return = json_decode($response_edit, true);

        Log::info($response_return);
        Log::info('【Finish Post Api】url:'.$url);

        return $response_return;
    }

    public static function sendDeleteApi($url, $header = null, $data = null)
    {
        Log::info('【Start Delete Api】url:'.$url);
        Log::info($data);

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
        $response_edit = strstr($response, '{');
        $response_return = json_decode($response_edit, true);

        Log::info($response_return);
        Log::info('【Finish Delete Api】url:'.$url);

        return $response_return;
    }

    public static function sendGetApi($url, $header)
    {
        Log::info('【Start Get Api】url:'.$url);
        Log::info($header);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $response_edit = strstr($response, '{');
        $response_return = json_decode($response_edit, true);

        Log::info($response_return);
        Log::info('【Finish Get Api】url:'.$url);

        return $response_return;
    }

    public static function convertISO8601ForKeysURL($carbon)
    {
        return $carbon->format('Y-m-d').'T'.$carbon->format('H:i').'+0900';
    }

    public static function convertISO8601ForAccessesURL($carbon)
    {
        return $carbon->format('Y-m-d').'T'.$carbon->format('H:i:s').'+09:00';
    }
}
