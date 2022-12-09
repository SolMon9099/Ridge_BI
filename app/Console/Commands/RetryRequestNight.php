<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use App\Service\DangerService;
use App\Service\PitService;
use App\Service\ShelfService;
use App\Service\ThiefService;
use App\Models\S3VideoHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Camera;

class RetryRequestNight extends Command
{
    protected $signature = 'ai:retry_request_night';

    protected $description = '動画解析リクエスト再送信';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $camera_start_on_time = config('const.camera_start_time');
        $camera_end_off_time = config('const.camera_end_time');
        $cur_time_object = new \DateTime();
        $cur_time_object->setTimezone(new \DateTimeZone('+0900')); //GMT
        $now = $cur_time_object->format('Y-m-d H:i:s');
        $now_date = $cur_time_object->format('Y-m-d');
        $now_hour = $cur_time_object->format('H');
        if ($now_hour >= 21) {
            $check_start_time = $now_date.' '.$camera_start_on_time;
            $check_end_time = $now_date.' '.$camera_end_off_time;
        } else {
            $prev_date = date('Y-m-d', strtotime(' -1 day'));
            $check_start_time = $prev_date.' '.$camera_start_on_time;
            $check_end_time = $prev_date.' '.$camera_end_off_time;
        }
        Log::info('check_start_time = '.$check_start_time);
        Log::info('check_end_time = '.$check_end_time);
        //download vidoe---------------
        $available_data = S3VideoHistory::query()
            ->where('request_id', '>', 0)
            ->where('status', 1)
            ->where('start_time', '>=', $check_start_time)
            ->where('start_time', '<=', $check_end_time)->get()->all();
        foreach ($available_data as $item) {
            $safie_service = new SafieApiService($item->contract_no);
            Log::info('Retry メディアファイル 作成要求取得');
            $media_status = $safie_service->getMediaFileStatus($item->device_id, $item->request_id);
            if ($media_status != null && isset($media_status['state']) && $media_status['state'] == 'AVAILABLE') {
                $video_data = $safie_service->downloadMediaFile($media_status['url']);
                if ($video_data != null && $video_data != 'not_found') {
                    Log::info('Retry メディアファイル ダウンロード成功********');
                    $start_time = $item->start_time;
                    $end_time = $item->end_time;
                    $start_date = date('Ymd', strtotime($start_time));
                    $file_name = date('YmdHis', strtotime($start_time)).'_'.date('YmdHis', strtotime($end_time)).'.mp4';
                    Storage::disk('s3')->put($item->device_id.'\\'.$start_date.'\\'.$file_name, $video_data);
                    $item->status = 2;
                    $item->file_path = $item->device_id.'/'.$start_date.'/'.$file_name;
                    $item->save();
                }
            }
            if ($media_status == 'not_found') {
                $item->delete();
            }
        }
        Log::info('Retry メディアファイル 作成要求削除(s3に保存されたもの)');
        $this->deleteOldRequests(null, $check_start_time);
        //------------------------------

        $failed_record_data = S3VideoHistory::query()
            ->where('request_id', 'error_503')
            ->where('start_time', '>=', $check_start_time)
            ->where('start_time', '<=', $check_end_time)->get()->all();
        if (count($failed_record_data) > 0 && (strtotime($now) < strtotime($check_end_time) + 11 * 60 * 60)) {
            $camera_data = [];

            foreach ($failed_record_data as $item) {
                if (isset($camera_data[$item->device_id])) {
                    continue;
                }
                $safie_service = new SafieApiService($item->contract_no);
                $camera_item = Camera::query()->where('camera_id', $item->device_id)->where('is_enabled', 1)->get()->first();
                if ($camera_item == null) {
                    continue;
                }
                $camera_data[$item->device_id] = $camera_item;

                Log::info('BI->AI用失敗したメディアファイル再作成要求');
                $start_datetime_object = new \DateTime($item->start_time, new \DateTimeZone('GMT+9'));
                $end_datetime_object = new \DateTime($item->end_time, new \DateTimeZone('GMT+9'));
                Log::info('retry start = '.$start_datetime_object->format('c'));
                Log::info('retry end = '.$end_datetime_object->format('c'));
                $request_id = $safie_service->makeMediaFile($item->device_id, $start_datetime_object->format('c'), $end_datetime_object->format('c'), '定期ダウンロード', $camera_item->reopened_at);
                Log::info('retry request id = '.$request_id);
                if ($request_id > 0) {
                    S3VideoHistory::query()->where('id', $item->id)->update(['request_id' => $request_id, 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
        } else {
            if (count($failed_record_data) > 0) {
                $camera_data = [];
                foreach ($failed_record_data as $item) {
                    if (isset($camera_data[$item->device_id])) {
                        continue;
                    }
                    $safie_service = new SafieApiService($item->contract_no);
                    $camera_item = Camera::query()->where('camera_id', $item->device_id)->where('is_enabled', 1)->get()->first();
                    if ($camera_item == null) {
                        continue;
                    }
                    $camera_data[$item->device_id] = $camera_item;

                    Log::info('BI->AI用失敗したメディアファイル再作成要求');
                    $start_datetime_object = new \DateTime($item->start_time, new \DateTimeZone('GMT+9'));
                    $end_datetime_object = new \DateTime($item->end_time, new \DateTimeZone('GMT+9'));
                    Log::info('retry start = '.$start_datetime_object->format('c'));
                    Log::info('retry end = '.$end_datetime_object->format('c'));
                    $request_id = $safie_service->makeMediaFile($item->device_id, $start_datetime_object->format('c'), $end_datetime_object->format('c'), '定期ダウンロード', $camera_item->reopened_at);
                    Log::info('retry request id = '.$request_id);
                    if ($request_id > 0) {
                        S3VideoHistory::query()->where('id', $item->id)->update(['request_id' => $request_id, 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                }
            }
            $retry_request_records = S3VideoHistory::query()
                ->where('status', '=', 3)
                ->where('request_id', '>', 0)
                ->where('start_time', '>=', $check_start_time)
                ->where('start_time', '<=', $check_end_time)
                ->orderBy('start_time')->get()->all();
            if (count($retry_request_records) > 0) {
                $camera_data = [];
                foreach ($retry_request_records as $retry_request_record) {
                    if (isset($camera_data[$retry_request_record->device_id])) {
                        continue;
                    }
                    $camera_item = Camera::query()->where('camera_id', $retry_request_record->device_id)->where('is_enabled', 1)->get()->first();
                    $camera_data[$retry_request_record->device_id] = $camera_item;
                    if ($camera_item != null) {
                        Log::info('BI->AI夜間api送信動画  '.$retry_request_record->start_time.' '.$retry_request_record->device_id);
                        $this->reqeuestToAI($retry_request_record->device_id, $camera_item->id, config('const.aws_url').$retry_request_record->file_path, 2);
                        S3VideoHistory::query()->where('id', $retry_request_record->id)->update(['status' => 4, 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                }
            }
        }

        return 0;
    }

    public function deleteOldRequests($contract_no = null, $start_time = null)
    {
        $query = S3VideoHistory::query()->where('status', '=', 2);
        if ($contract_no != null) {
            $query->where('contract_no', $contract_no);
        }
        if ($start_time != null) {
            $query->where('start_time', '>=', $start_time);
        }
        $data = $query->orderBy('updated_at')->get();
        $safie_service = new SafieApiService($contract_no);
        if (count($data) > 0) {
            foreach ($data as $item) {
                $res = $safie_service->deleteMediaFile($item->device_id, $item->request_id);
                if ($res == 200 || $res == 404) {
                    $item->status = 3;
                    $item->save();
                }
            }
        }
    }

    public function reqeuestToAI($device_id, $id_camera, $movie_path = null, $is_night = 1)
    {
        if (!Storage::disk('temp')->exists('retry_ai_request')) {
            Storage::disk('temp')->makeDirectory('retry_ai_request');
        }
        $url = '';
        $header = [
            'Content-Type: application/json',
        ];
        //2. 危険エリア侵入検知解析リクエスト（BI→AI） api/v1/danger-zone/register-camera
        $rules = DangerService::getRulesByCameraID($id_camera);
        if (count($rules) > 0) {
            $params = [];
            foreach ($rules as $rule) {
                if (!isset($params['camera_info'])) {
                    $params['camera_info'] = [];
                }
                $params['camera_info']['camera_id'] = $device_id;
                if (!isset($params['movie_info'])) {
                    $params['movie_info'] = [];
                }
                $params['movie_info']['movie_path'] = $movie_path;
                if (!isset($params['rect_info'])) {
                    $params['rect_info'] = [];
                }

                $params['rect_info']['rect_id'] = (string) $rule->id;
                $params['rect_info']['rect_point_array'] = json_decode($rule->points);
                $action_data = [];
                foreach (json_decode($rule->action_id) as $action_code) {
                    $action_data[] = (int) $action_code;
                }
                $params['rect_info']['action_id'] = $action_data;
                $params['priority'] = 1;
                $params['request_type'] = $is_night;
                Log::info('Retry------危険エリア侵入検知解析リクエスト（BI→AI）開始');
                $url = config('const.ai_server').'danger-zone/register-camera';

                $ai_res = $this->sendPostApi($url, $header, $params, 'json');
                //save unsuccess api to AI------------------------
                if ($ai_res != 200) {
                    if ($ai_res == 503 || $ai_res == 530) {
                        Log::info('Retry------解析を止める用API（BI→AI）開始');
                        $stop_url = config('const.ai_server').'stop-analysis';
                        $stop_params = [];
                        $stop_params['camera_info'] = [];
                        $stop_params['camera_info']['camera_id'] = $device_id;
                        $stop_params['camera_info']['rule_name'] = 'danger_zone';
                        $stop_params['priority'] = 1;
                        $stop_params['request_type'] = $is_night;
                        $this->sendPostApi($stop_url, $header, $stop_params, 'json');
                        Log::info('Retry------解析を止める用API（BI→AI）中止');
                    }
                    $params['request_type'] = 2;
                    $temp_save_data = [
                        'url' => $url,
                        'params' => $params,
                        'type' => 'danger',
                        'device_id' => $device_id,
                    ];
                    Storage::disk('temp')->put('retry_ai_request\\'.'danger_'.$device_id.'_'.date('YmdHis').'.json', json_encode($temp_save_data));
                }
                //-------------------------------------------------
            }
        }
        //--------------------------------------

        //４．ピット入退場解析リクエスト（BI→AI） /api/v1/pit/register-camera
        $rules = PitService::getRulesByCameraID($id_camera);
        if (count($rules) > 0) {
            if (count($rules) > 0) {
                $params = [];
                foreach ($rules as $rule) {
                    if (!isset($params['camera_info'])) {
                        $params['camera_info'] = [];
                    }
                    $params['camera_info']['camera_id'] = $device_id;
                    if (!isset($params['movie_info'])) {
                        $params['movie_info'] = [];
                    }
                    $params['movie_info']['movie_path'] = $movie_path;
                    if (!isset($params['rect_info'])) {
                        $params['rect_info'] = [];
                    }
                    $params['rect_info']['rect_id'] = (string) $rule->id;
                    $params['rect_info']['entrance_rect_point_array'] = json_decode($rule->red_points);
                    $params['rect_info']['exit_rect_point_array'] = json_decode($rule->blue_points);
                    $params['priority'] = 1;
                    $params['request_type'] = $is_night;
                    Log::info('Retry------ピット入退場解析リクエスト（BI→AI）開始');
                    $url = config('const.ai_server').'pit/register-camera';
                    $ai_res = $this->sendPostApi($url, $header, $params, 'json');
                    //save unsuccess api to AI------------------------
                    if ($ai_res != 200) {
                        if ($ai_res == 503 || $ai_res == 530) {
                            Log::info('Retry------解析を止める用API（BI→AI）開始');
                            $stop_url = config('const.ai_server').'stop-analysis';
                            $stop_params = [];
                            $stop_params['camera_info'] = [];
                            $stop_params['camera_info']['camera_id'] = $device_id;
                            $stop_params['camera_info']['rule_name'] = 'ee_count';
                            $stop_params['priority'] = 1;
                            $stop_params['request_type'] = $is_night;
                            $this->sendPostApi($stop_url, $header, $stop_params, 'json');
                            Log::info('Retry------解析を止める用API（BI→AI）中止');
                        }
                        $params['request_type'] = 2;
                        $temp_save_data = [
                            'url' => $url,
                            'params' => $params,
                            'type' => 'pit',
                            'device_id' => $device_id,
                        ];
                        Storage::disk('temp')->put('retry_ai_request\\'.'pit_'.$device_id.'_'.date('YmdHis').'.json', json_encode($temp_save_data));
                    }
                    //-------------------------------------------------
                }
            }
        }
        //--------------------------------------
        // //６．棚乱れ解析リクエスト（BI→AI） /api/v1/shelf-theft/register-camera
        // $rules = ShelfService::getRulesByCameraID($id_camera);
        // if (count($rules) > 0) {
        //     if (count($rules) > 0) {
        //         $params = [];
        //         foreach ($rules as $rule) {
        //             if (!isset($params['camera_info'])) {
        //                 $params['camera_info'] = [];
        //             }
        //             $params['camera_info']['camera_id'] = $device_id;
        //             if (!isset($params['movie_info'])) {
        //                 $params['movie_info'] = [];
        //             }

        //             $params['movie_info']['movie_path'] = $movie_path;
        //             if (!isset($params['rect_info'])) {
        //                 $params['rect_info'] = [];
        //             }
        //             $rect_param = [];
        //             $rect_param['rect_id'] = (string) $rule->id;
        //             $rect_param['rect_point_array'] = json_decode($rule->points);
        //             $params['rect_info'][] = $rect_param;
        //             $params['priority'] = 1;
        //             $params['request_type'] = $is_night;
        //         }
        //         Log::info('棚乱れ解析リクエスト（BI→AI）開始ーーーー');
        //         $url = config('const.ai_server').'shelf-theft/register-camera';
        //         $ai_res = $this->sendPostApi($url, $header, $params, 'json');
        //         //save unsuccess api to AI------------------------
        //         if ($ai_res != 200) {
                        // if ($ai_res == 503 || $ai_res == 530) {
                        // } else {
                        // $params['request_type'] = 2;
                        // $temp_save_data = [
                        //     'url' => $url,
                        //     'params' => $params,
                        //     'type' => 'shelf',
                        // ];
                        // Storage::disk('temp')->put('retry_ai_request\\'.'shelf_'.$device_id.'_'.date('YmdHis').'.json', json_encode($temp_save_data));
                        // }
        //         }
        //         //-------------------------------------------------
        //     }
        // }
        // //--------------------------------------

        // //９．大量盗難解析リクエスト（BI→AI） /api/v1/hanger-counter/register-camera
        // $rules = ThiefService::getRulesByCameraID($id_camera);
        // if (count($rules) > 0) {
        //     if (count($rules) > 0) {
        //         $params = [];
        //         foreach ($rules as $rule) {
        //             if (!isset($params['camera_info'])) {
        //                 $params['camera_info'] = [];
        //             }
        //             $params['camera_info']['camera_id'] = $device_id;
        //             if (!isset($params['movie_info'])) {
        //                 $params['movie_info'] = [];
        //             }
        //             $params['movie_info']['movie_path'] = $movie_path;
        //             if (!isset($params['rect_info'])) {
        //                 $params['rect_info'] = [];
        //             }
        //             $rect_param = [];
        //             $rect_param['rect_id'] = (string) $rule->id;
        //             $rect_param['color_code'] = $rule->hanger;
        //             $rect_param['rect_point_array'] = json_decode($rule->points);
        //             $params['rect_info'][] = $rect_param;
        //             $params['priority'] = 1;
        //             $params['request_type'] = $is_night;
        //         }
        //         Log::info('大量盗難解析リクエスト（BI→AI）開始ーーーー');
        //         $url = config('const.ai_server').'hanger-counter/register-camera';
        //         $ai_res = $this->sendPostApi($url, $header, $params, 'json');
        //         //save unsuccess api to AI------------------------
        //         if ($ai_res != 200) {
                    // if ($ai_res == 503 || $ai_res == 530) {
                    // } else {
                    //             $params['request_type'] = 2;
        //             $temp_save_data = [
        //                 'url' => $url,
        //                 'params' => $params,
        //                 'type' => 'thief',
        //             ];
        //             Storage::disk('temp')->put('retry_ai_request\\'.'thief_'.$device_id.'_'.date('YmdHis').'.json', json_encode($temp_save_data));
                        // }
        //         }
        //         //-------------------------------------------------
        //     }
        // }
        // //--------------------------------------
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
