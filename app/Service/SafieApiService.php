<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;

class SafieApiService
{
    private $client_id = 'dc2537ffb887';
    private $secret = 'd25100e130499d0fb257df19cd5b0279';

    private $api_url = 'https://openapi.safie.link/v1/';
    private $redirect_uri = 'http://54.95.92.211/';     //http://120.143.15.36/
    private $device_id = 'FvY6rnGWP12obPgFUj0a';
    private $auth_authorize = 'auth/authorize';
    private $auth_token = 'auth/token';

    private $session_id = null;
    private $auth_id = null;
    private $pin = null;
    private $status = null;

    public function __construct()
    {
    }

    public function set_content($content)
    {
        $this->sms_content = $content;
        Log::debug("--- {$this->sms_content} 送信内容準備 ---");
    }

    public function getAccessToken($code)
    {
        $url = sprintf('%s%s', $this->api_url, $this->auth_token);  //https://openapi.safie.link/v1/auth/token
        $params = [];
        $params['client_id'] = $this->client_id;   //'dc2537ffb887'
        $params['client_secret'] = $this->secret;   //'d25100e130499d0fb257df19cd5b0279'
        $params['grant_type'] = 'authorization_code';
        $params['redirect_uri'] = $this->redirect_uri;
        $params['code'] = $code;    //認可コード
        $response = $this->sendPostApi($url, null, $params, false);

        return $response;
    }

    public function getAuthUrl()
    {
        $url = sprintf('%s%s', $this->api_url, $this->auth_authorize);
        $params = [];
        $params['client_id'] = $this->client_id;
        $params['response_type'] = 'code';
        $params['scope'] = 'safie-api';
        $params['redirect_uri'] = $this->redirect_uri;
        $params['state'] = '';
        $url = sprintf('%s?%s', $url, http_build_query($params));

        return $url;
    }

    public function getDeviceImage($access_token)
    {
        $device_id = $this->device_id;
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/image', $device_id);

        $header = [
            'Authorization: Bearer '.$access_token,
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

    /**
     * Step 1 認証リクエスト(session.login).
     */
    public function session_login()
    {
        $url = sprintf('%s?method=%s&api_key=%s&format=json', $this->api_url, 'session.login', $this->api_key);
        $result = $this->callApi($url);
        Log::debug('-- session_login ---');
        $ret = true;
        if (empty($result)) {
            $ret = false;
        }
        if (isset($result->error)) {
            $ret = false;
        }
//        var_dump($result->{'@attributes'}->success);
        if ($ret) {
            $this->session_id = $result->session_id;
        } else {
            $error = var_export($result, true);
            Log::debug($error);
        }

        return $ret;
    }

    /**
     * Step 2 本人認証状態の問合せ(auth.check).
     */
    public function auth_check()
    {
        $url = sprintf('%s?method=%s&session_id=%s&tel=%s&format=json', $this->api_url, 'auth.check', $this->session_id, $this->tel);
        $result = $this->callApi($url);
        Log::debug('-- auth_check ---');
        if (empty($result)) {
            return false;
        }
        if (isset($result->error)) {
            return false;
        }
        $this->status = $result->status;
        $this->auth_id = $result->auth_id;
        $this->pin = isset($result->pin) ? $result->pin : null;

        //1: 未認証 2: 本人認証済み
        if ($this->status == 2) {
            return true;
        }

        return $this->auth_complete();
    }

    /**
     * Step 3 本人確認完了を通知(auth.complete).
     */
    public function auth_complete()
    {
        Log::debug('-- auth_complete ---');
        $url = sprintf('%s?method=%s&auth_id=%s&session_id=%s&format=json', $this->api_url, 'auth.complete', $this->auth_id, $this->session_id, $this->pin);
        $result = $this->callApi($url);
        if (empty($result)) {
            return false;
        }
        if (isset($result->error)) {
            return false;
        }

        return true;
    }

    /**
     * Step 4 SMS を送信(sms.send).
     */
    public function sms_send()
    {
        $sms_title = '24esthe';
        $url = sprintf('%s?method=%s&auth_id=%s&session_id=%s&sms_title=%s&sms_text=%s&format=json', $this->api_url, 'sms.send', $this->auth_id, $this->session_id, $sms_title, $this->sms_content);

        $len = strlen($this->sms_content);
        Log::debug("--- sms_send  {$this->sms_content} {$len}---　送信");

        $result = $this->callApi($url);
        $ret = true;
        if (empty($result)) {
            $ret = false;
        } elseif (isset($result->error)) {
            $ret = false;
        }
        $res = $ret ? '成功' : '失敗';
        if (!$ret) {
            $res .= ' '.(isset($result->note) ? $result->note : '');
        }

        Log::debug("---  {$this->sms_content} ---　{$res}");

        return $ret;
    }

    // リフレッシュトークンの再発行
    // public static function regenerateRefreshToken()
    // {
    //     $akerun_token = AkerunToken::latest()->first();
    //     $url = 'https://api.akerun.com/oauth/token';
    //     $data = array(
    //         'grant_type' => 'refresh_token',
    //         'client_id' => config('akerun.client_id'),
    //         'client_secret' => config('akerun.client_secret'),
    //         'refresh_token' => $akerun_token->refresh_token,
    //     );

    //     $response_return = self::sendPostApi($url, null, $data);

    //     AkerunToken::create([
    //         'access_token' => $response_return["access_token"],
    //         'refresh_token' => $response_return["refresh_token"],
    //     ]);
    // }

    public function sendPostApi($url, $header = null, $data = null, $xform = false)
    {
        Log::info('【Start Post Api】url:'.$url);
        Log::info($data);

        $curl = curl_init($url);
        //POSTで送信
        curl_setopt($curl, CURLOPT_POST, true);

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
        // curl_close($curl);
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
