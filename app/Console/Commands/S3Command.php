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
use App\Models\MediaRequestHistory;
use Illuminate\Support\Facades\DB;

class S3Command extends Command
{
    protected $signature = 's3:video_get_save';

    protected $description = 'S3に動画データ保存';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $camera_start_on_time = config('const.camera_start_time');
        $camera_end_off_time = config('const.camera_end_time');
        $request_interval = config('const.request_interval');
        $cur_time_object = new \DateTime();
        $cur_time_object->setTimezone(new \DateTimeZone('+0900')); //GMT
        $now = $cur_time_object->format('Y-m-d H:i:s');
        $now_date = $cur_time_object->format('Y-m-d');

        Log::info('カメラチェック開始');
        $cameras = Camera::query()->get()->all();
        if (count($cameras) == 0) {
            return 0;
        }
        $record_start_time_object = clone $cur_time_object;
        // $record_start_time_object->sub(new \DateInterval('PT'.(string) 2 * $request_interval.'M'));
        $record_start_time_object->sub(new \DateInterval('PT'.(string) ($request_interval + 3).'M'));
        $record_start_time = $record_start_time_object->format('c');
        $record_end_time_object = clone $cur_time_object;
        // $record_end_time_object->sub(new \DateInterval('PT'.(string) $request_interval.'M'));
        $record_end_time_object->sub(new \DateInterval('PT'.(string) (3).'M'));
        $record_end_time = $record_end_time_object->format('c');

        foreach ($cameras as $camera) {
            if ($camera->contract_no == null) {
                continue;
            }
            $safie_service = new SafieApiService($camera->contract_no);

            //メディアファイル 作成要求削除(s3に保存されたもの)
            Log::info('メディアファイル 作成要求削除(s3に保存されたもの)ーーーー');
            $this->deleteOldRequests($camera->contract_no);

            //メディアファイル作成要求一覧取得・S3に保存--------------------------------
            Log::info('メディアファイル作成要求一覧取得・S3に保存ーーーー');
            $this->saveMedia($camera);
            //----------------------------------------------------------------------
            if ($camera->is_enabled == 1 && strtotime($now_date.' '.$camera_start_on_time) + ($request_interval + 3) * 60 <= strtotime($now) && ($request_interval + 3) * 60 + strtotime($now_date.' '.$camera_end_off_time) >= strtotime($now)) {
                //メディアファイル作成要求--------------------------------
                Log::info('メディアファイル作成要求--------------------------------');
                $request_id = $safie_service->makeMediaFile($camera->camera_id, $record_start_time, $record_end_time, '定期ダウンロード', $camera->reopened_at);
                Log::info('request_id = '.$request_id);
                if ($request_id > 0) {
                    $this->createS3History($request_id, $record_start_time_object, $record_end_time_object, $camera->camera_id, $camera->contract_no);
                } else {
                    if ($request_id != null) {
                        $http_code = str_replace('http_code_', '', $request_id);
                        if ($http_code == 503) {
                            $this->createS3History('error_503', $record_start_time_object, $record_end_time_object, $camera->camera_id, $camera->contract_no);
                        }
                    }
                }
            }
            //--------------------------------------------------------
        }

        //--------test--------pit------------
        $test_path_array = [
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107150625_20221107150636.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107150705_20221107150716.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107150824_20221107150836.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107150909_20221107150927.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107151819_20221107151835.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152141_20221107152146.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152145_20221107152156.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152206_20221107152213.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152438_20221107152448.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152628_20221107152635.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152730_20221107152741.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152820_20221107152821.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107152853_20221107152855.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107153005_20221107153008.mp4',
            'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_movie/20221107153043_20221107153050.mp4',
        ];
        $params = [];
        $header = [
            'Content-Type: application/json',
        ];

        if (Storage::disk('temp')->exists('test_pit.json')) {
            $test_index = Storage::disk('temp')->get('test_pit.json');
            $test_index = (int) $test_index;
            if ($test_index < 15) {
                Storage::disk('temp')->put('test_pit.json', $test_index + 1);
            }
        } else {
            Storage::disk('temp')->put('test_pit.json', 1);
            $test_index = 0;
        }

        $rule = DB::table('pit_detection_rules')->where('id', 74)->get()->first();
        if (isset($test_path_array[$test_index])) {
            Log::info('テスト用ピット入退場解析リクエスト（BI→AI）開始ーーーー'.$test_index.')');
            $path = $test_path_array[$test_index];
            if (!isset($params['camera_info'])) {
                $params['camera_info'] = [];
            }
            $params['camera_info']['camera_id'] = 'FvY6rnGWP12obPgFUj0a';
            if (!isset($params['movie_info'])) {
                $params['movie_info'] = [];
            }
            $params['movie_info']['movie_path'] = $path;
            if (!isset($params['rect_info'])) {
                $params['rect_info'] = [];
            }
            $params['rect_info']['rect_id'] = '74';
            $params['rect_info']['entrance_rect_point_array'] = json_decode($rule->red_points);
            $params['rect_info']['exit_rect_point_array'] = json_decode($rule->blue_points);
            $params['priority'] = 1;

            $url = config('const.ai_server').'pit/register-camera';
            $this->sendPostApi($url, $header, $params, 'json');
        }

        //-----------------------------------

        return 0;
    }

    public function deleteOldRequests($contract_no = null)
    {
        $query = S3VideoHistory::query()->where('status', '=', 2);
        if ($contract_no != null) {
            $query->where('contract_no', $contract_no);
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

    public function createS3History($request_id, $record_start_time_object, $record_end_time_object, $device_id = null, $contract_no = null)
    {
        $record = new S3VideoHistory();
        $record->request_id = $request_id;
        $record->device_id = $device_id == null ? 'FvY6rnGWP12obPgFUj0a' : $device_id;
        $record->start_time = $record_start_time_object->format('Y-m-d H:i:s');
        $record->end_time = $record_end_time_object->format('Y-m-d H:i:s');
        $record->contract_no = $contract_no;
        $record->save();
    }

    public function saveMedia($camera_item)
    {
        $device_id = $camera_item->camera_id;
        $data = S3VideoHistory::query()->where('device_id', $device_id)
            ->where('contract_no', $camera_item->contract_no)
            ->where('status', '!=', 2)
            ->where('status', '!=', 3)
            ->where('status', '!=', 4)
            ->orderBy('start_time')->get()->all();
        $safie_service = new SafieApiService($camera_item->contract_no);
        $retry_count_index = 0;
        $camera_start_on_time = config('const.camera_start_time');
        $camera_end_off_time = config('const.camera_end_time');
        $request_interval = config('const.request_interval');
        $cur_time_object = new \DateTime();
        $cur_time_object->setTimezone(new \DateTimeZone('+0900')); //GMT
        $now = $cur_time_object->format('Y-m-d H:i:s');
        $now_date = $cur_time_object->format('Y-m-d');
        foreach ($data as $item) {
            if (!((int) $item->request_id > 0)) {
                // $error_code = (int) str_replace('error_', '', $item->request_id);
                // if ($error_code == 400 || $error_code == 403 || $error_code == 404) {
                //     $item->delete();
                //     continue;
                // }
                // if ($camera_item->is_enabled != 1) {
                //     continue;
                // }
                // if (!(strtotime($now_date.' '.$camera_start_on_time) <= strtotime($now) && strtotime($now_date.' '.$camera_end_off_time) >= strtotime($now))) {
                //     if ($retry_count_index > 0) {
                //         continue;
                //     }
                //     ++$retry_count_index;
                //     Log::info('BI->AI用失敗したメディアファイル再作成要求');
                //     $start_datetime_object = new \DateTime($item->start_time, new \DateTimeZone('GMT+9'));
                //     $end_datetime_object = new \DateTime($item->end_time, new \DateTimeZone('GMT+9'));
                //     Log::info('retry start = '.$start_datetime_object->format('c'));
                //     Log::info('retry end = '.$end_datetime_object->format('c'));
                //     $request_id = $safie_service->makeMediaFile($item->device_id, $start_datetime_object->format('c'), $end_datetime_object->format('c'), '定期ダウンロード', $camera_item->reopened_at);
                //     Log::info('retry request id = '.$request_id);
                //     if ($request_id > 0) {
                //         $this->createS3History($request_id, $start_datetime_object, $end_datetime_object, $item->device_id, $item->contract_no);
                //         $item->delete();
                //     } else {
                //         if ($request_id != 'http_code_404' && $request_id != null) {
                //             continue;
                //         }
                //         if ($request_id == 'http_code_404') {
                //             Log::info('delete history '.$item->start_time);
                //             MediaRequestHistory::query()->where('start_time', $item->start_time)->where('http_code', 404)->where('device_id', $item->device_id)->delete();
                //         }
                //         $item->delete();
                //     }
                //     Log::info('BI->AI用失敗したメディアファイル再作成終了');
                // }
            } else {
                Log::info('メディアファイル 作成要求取得ーーーー');
                $media_status = $safie_service->getMediaFileStatus($device_id, $item->request_id);
                if ($media_status != null && isset($media_status['state'])) {
                    if ($media_status['state'] == 'AVAILABLE') {
                        if ($item->status == 0) {
                            $item->status = 1;
                            $item->save();
                        }
                        $video_data = $safie_service->downloadMediaFile($media_status['url']);
                        if ($video_data != null && $video_data != 'not_found') {
                            Log::info('メディアファイル ダウンロード成功ーーーー');
                            $start_time = $item->start_time;
                            $end_time = $item->end_time;
                            $start_date = date('Ymd', strtotime($start_time));
                            $file_name = date('YmdHis', strtotime($start_time)).'_'.date('YmdHis', strtotime($end_time)).'.mp4';
                            Storage::disk('s3')->put($device_id.'\\'.$start_date.'\\'.$file_name, $video_data);
                            $item->status = 2;
                            $item->file_path = $device_id.'/'.$start_date.'/'.$file_name;
                            $item->save();
                            //request rule data to AI--------
                            $aws_url = config('const.aws_url');
                            $movie_path = $aws_url.$device_id.'/'.$start_date.'/'.$file_name;
                            $is_night = 1;  //no night
                            if (strtotime($now_date.' '.$camera_start_on_time) + ($request_interval + 3)*60 <= strtotime($now) && ($request_interval + 3)*60 + strtotime($now_date.' '.$camera_end_off_time) >= strtotime($now)) {
                                $this->reqeuestToAI($device_id, $camera_item->id, $movie_path, $is_night);
                            }
                            //-------------------------------
                        }
                        if ($video_data == 'not_found') {
                            $item->delete();
                        }
                    }
                }
                if ($media_status == 'not_found') {
                    $item->delete();
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
                Log::info('危険エリア侵入検知解析リクエスト（BI→AI）開始ーーーー');
                $url = config('const.ai_server').'danger-zone/register-camera';

                $ai_res = $this->sendPostApi($url, $header, $params, 'json');
                //save unsuccess api to AI------------------------
                if ($ai_res != 200) {
                    if ($ai_res == 503 || $ai_res == 530) {
                        Log::info('解析を止める用API（BI→AI）開始--------');
                        $stop_url = config('const.ai_server').'stop-analysis';
                        $stop_params = [];
                        $stop_params['camera_info'] = [];
                        $stop_params['camera_info']['camera_id'] = $device_id;
                        $stop_params['camera_info']['rule_name'] = 'danger_zone';
                        $stop_params['priority'] = 1;
                        $stop_params['request_type'] = $is_night;
                        $this->sendPostApi($stop_url, $header, $stop_params, 'json');
                        Log::info('解析を止める用API（BI→AI）中止--------');
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
                    Log::info('ピット入退場解析リクエスト（BI→AI）開始ーーーー');
                    $url = config('const.ai_server').'pit/register-camera';
                    $ai_res = $this->sendPostApi($url, $header, $params, 'json');
                    //save unsuccess api to AI------------------------
                    if ($ai_res != 200) {
                        if ($ai_res == 503 || $ai_res == 530) {
                            Log::info('解析を止める用API（BI→AI）開始--------');
                            $stop_url = config('const.ai_server').'stop-analysis';
                            $stop_params = [];
                            $stop_params['camera_info'] = [];
                            $stop_params['camera_info']['camera_id'] = $device_id;
                            $stop_params['camera_info']['rule_name'] = 'ee_count';
                            $stop_params['priority'] = 1;
                            $stop_params['request_type'] = $is_night;
                            $this->sendPostApi($stop_url, $header, $stop_params, 'json');
                            Log::info('解析を止める用API（BI→AI）中止--------');
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
