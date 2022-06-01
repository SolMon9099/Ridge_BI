<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;

class SafieApiService
{
    private $client_id = 'dc2537ffb887';
    private $secret = 'd25100e130499d0fb257df19cd5b0279';

    private $api_url = 'https://openapi.safie.link/v1/';
    private $auth_authorize = 'auth/authorize';

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

    public function auth_authorize()
    {
        $url = sprintf('%s%s', $this->api_url, $this->auth_authorize);
        $params = [];
        $params['client_id'] = $this->client_id;
        $params['response_type'] = 'code';
        $params['scope'] = 'safie-api';
        $params['redirect_uri'] = 'http://127.0.0.1:8000';
        $params['state'] = '';
        $url = sprintf('%s?%s', $url, http_build_query($params));
        // var_dump($url);
        // exit;
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
        var_dump($response);
        exit;
    }

    public function send()
    {
        $url = sprintf('%s%s', $this->api_url, $this->auth_authorize);
        $params = [];
        $params['client_id'] = $this->client_id;
        $params['response_type'] = 'code';
        $params['scope'] = 'safie-api';
        $params['redirect_uri'] = 'http://127.0.0.1:8000';
        $params['state'] = '';
        // $url = sprintf('%s?%s', $url, http_build_query($params));
        /*
        device_id (device_serial_code): FvY6rnGWP12obPgFUj0a
        {
            "access_token": "c2e0473a-6b4c-4ce5-b943-d51cfaa96550",
            "token_type": "Bearer",
            "expires_in": 604800,
            "refresh_token": "55263baf-afea-49d4-bc79-64f6d1980b89"
        }
        */
        $device_id = 'FvY6rnGWP12obPgFUj0a';
        $url = sprintf('https://openapi.safie.link/v1/devices/%s/image', $device_id);
        // var_dump($url);
        // exit;
        $access_token = 'c2e0473a-6b4c-4ce5-b943-d51cfaa96550';
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
        var_dump($response);
        exit;
    }

    public function send_content()
    {
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
}
