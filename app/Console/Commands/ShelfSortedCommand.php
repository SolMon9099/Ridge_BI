<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\ShelfDetectionRule;
use App\Models\Camera;
use App\Models\Token;

class ShelfSortedCommand extends Command
{
    protected $signature = 's3:sorted_image_save';

    protected $description = 'S3に棚乱れ検知用整理済み画像保存';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // ini_set('memory_limit', '4096M');
        // $s3_files = Storage::disk('s3')->files('test_movie');
        // foreach ($s3_files as $s3file) {
        //     Storage::disk('s3')->delete($s3file);
        // }
        // $files = Storage::disk('video')->files('test_movie');
        // foreach ($files as $file_name) {
        //     $file_content = Storage::disk('video')->get($file_name);
        //     Storage::disk('s3')->put($file_name, $file_content);
        // }

        Log::info('定時撮影チェック開始ーーーーーー');
        $shelf_detection_rules = ShelfDetectionRule::select('shelf_detection_rules.*', 'cameras.camera_id as device_id', 'cameras.contract_no')
            ->leftJoin('cameras', 'cameras.id', 'shelf_detection_rules.camera_id')->get()->unique('device_id');
        if (count($shelf_detection_rules) > 0) {
            $now_hour = (int) date('H');
            if ($now_hour > 23) {
                $now_hour = $now_hour - 24;
            }
            $now_min = date('i');
            foreach ($shelf_detection_rules as $item) {
                if ((int) $item->hour == $now_hour && (int) $item->mins == $now_min) {
                    Log::info('save image start = '.$now_hour.' : '.$now_min);
                    $safie_service = new SafieApiService($item->contract_no);
                    $camera_image_data = $safie_service->getDeviceImage($item->device_id);
                    if ($camera_image_data != null) {
                        $file_name = date('YmdHis').'.jpeg';
                        $date = date('Ymd');
                        $device_id = $item->device_id;
                        Storage::disk('s3')->put('shelf_sorted/'.$device_id.'/'.$date.'/'.$file_name, $camera_image_data);
                        Log::info('image_ur = '.'shelf_sorted/'.$device_id.'/'.$date.'/'.$file_name);
                    }
                }
            }
        }
        Log::info('定時撮影チェック終了ーーーーーー');

        //------------------------------------------------------------------------------------
        Log::info('カメラトークン送信開始ーーーーーー');
        $cameras = Camera::query()->get()->all();
        if (count($cameras) == 0) {
            return 0;
        }
        $send_url = 'http://43.206.48.25/api/v1/camera_token/send';
        $send_params['camera_token_info'] = [];
        $token_data = [];
        foreach ($cameras as $camera) {
            $one_data = [];
            if ($camera->camera_id != 'FvY6rnGWP12obPgFUj0a') {
                $one_data['camera_id'] = $camera->camera_id;
                $one_data['serial_no'] = $camera->serial_no;
            } else {
                $spec_one_data = [];
                $spec_one_data['camera_id'] = $camera->camera_id;
                $spec_one_data['serial_no'] = $camera->serial_no;
            }

            if (!isset($token_data[$camera->contract_no])) {
                $token_record = Token::query()->where('contract_no', $camera->contract_no)->get()->first();
                if ($token_record != null) {
                    $token_data[$camera->contract_no] = $token_record->access_token;
                }
            }
            if (isset($token_data[$camera->contract_no])) {
                if ($camera->camera_id != 'FvY6rnGWP12obPgFUj0a') {
                    $one_data['access_token'] = $token_data[$camera->contract_no];
                    $send_params['camera_token_info'][] = $one_data;
                } else {
                    $spec_one_data['access_token'] = $token_data[$camera->contract_no];
                }
            }
        }
        $header = [
            'Content-Type: application/json',
        ];
        if (count($send_params['camera_token_info']) > 0) {
            $ai_res = $this->sendPostApi($send_url, $header, $send_params, 'json');
        }
        if (isset($spec_one_data) && isset($spec_one_data['camera_id'])) {
            $send_params['camera_token_info'] = [];
            $send_params['camera_token_info'][] = $spec_one_data;
            $send_url = 'http://3.114.15.58/api/v1/camera_token/send';
            $ai_res = $this->sendPostApi($send_url, $header, $send_params, 'json');
        }
        Log::info('カメラトークン送信終了ーーーーーー');

        return 0;
    }

    public function sendPostApi($url, $header = null, $data = null, $request_type = 'query')
    {
        Log::info('【Start Post Api for AI】url:'.$url);

        $curl = curl_init($url);
        //POSTで送信
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($data) {
            switch ($request_type) {
                case 'query':
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    break;
                case 'json':
                    Log::info('post param data ='.json_encode($data));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
            }
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode = '.$httpcode);
        curl_close($curl);

        return $httpcode;
    }
}
