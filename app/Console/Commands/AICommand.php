<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\DangerAreaDetection;
use App\Models\ShelfDetection;
use App\Models\PitDetection;
use App\Models\ThiefDetection;
use App\Service\CameraService;
use Illuminate\Support\Facades\DB;

class AICommand extends Command
{
    protected $signature = 'ai:check_request_download';

    protected $description = 'AIが検知したデータ保存';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('check detection json file exist****************');
        if (!Storage::disk('temp')->exists('retry_video_request')) {
            Storage::disk('temp')->makeDirectory('retry_video_request');
        }
        $all_files = Storage::disk('temp')->files('video_request');
        if (count($all_files) > 0) {
            Log::info('ダウンロードjsonチェック開始****************');
            foreach ($all_files as $file) {
                $file_content = Storage::disk('temp')->get($file);
                if ($file_content != null) {
                    $file_content = json_decode($file_content);
                }
                if (isset($file_content->request_id) && $file_content->request_id > 0) {
                    $safie_service = new SafieApiService($file_content->contract_no);
                    $media_status = $safie_service->getMediaFileStatus($file_content->device_id, $file_content->request_id);
                    if ($media_status != null && isset($media_status['state'])) {
                        Log::info($media_status);
                        if ($media_status['state'] !== 'AVAILABLE') {
                            continue;
                        }
                        if ($media_status['state'] == 'AVAILABLE') {
                            $video_data = $safie_service->downloadMediaFile($media_status['url']);
                            if ($video_data == null) {
                                continue;
                            }
                            if ($video_data == 'not_found') {
                                Storage::disk('temp')->delete($file);
                                continue;
                            }
                            Log::info('video ok~~~~~~~');
                            $thumb_image = $safie_service->getDeviceImage($file_content->device_id, $file_content->starttime_format_for_image);
                            $file_name = date('YmdHis', strtotime($file_content->starttime)).'.mp4';

                            $type = $file_content->type;
                            $folder_name = 'danger';
                            switch ($type) {
                                case 'danger_area':
                                    $folder_name = 'danger';
                                    $detection_model = new DangerAreaDetection();
                                    if (isset($file_content->detection_action_id)) {
                                        $detection_model->detection_action_id = (int) $file_content->detection_action_id;
                                    }
                                    break;
                                case 'shelf':
                                    $folder_name = 'shelf';
                                    $detection_model = new ShelfDetection();
                                    break;
                                case 'pit':
                                    $folder_name = 'pit';
                                    $detection_model = new PitDetection();
                                    if (isset($file_content->nb_exit)) {
                                        $detection_model->nb_exit = (int) $file_content->nb_exit;
                                    }
                                    if (isset($file_content->nb_entry)) {
                                        $detection_model->nb_entry = (int) $file_content->nb_entry;
                                    }
                                    break;
                                case 'thief':
                                    $folder_name = 'thief';
                                    $detection_model = new ThiefDetection();
                                    break;
                            }
                            Storage::disk('video')->put($folder_name.'\\'.$file_content->device_id.'\\'.$file_name, $video_data);

                            $detection_model->camera_id = $file_content->camera_id;
                            $detection_model->rule_id = $file_content->rule_id;
                            $detection_model->video_file_path = $folder_name.'/'.$file_content->device_id.'/'.$file_name;
                            $detection_model->starttime = $file_content->starttime;
                            $detection_model->endtime = $file_content->endtime;
                            if ($thumb_image != null) {
                                Storage::disk('thumb')->put($folder_name.'\\'.$file_content->device_id.'\\'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg', $thumb_image);
                                $detection_model->thumb_img_path = $folder_name.'/'.$file_content->device_id.'/'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg';
                            }
                            $detection_model->save();
                            $safie_service->deleteMediaFile($file_content->device_id, $file_content->request_id);
                            Storage::disk('temp')->delete($file);
                        }
                    }
                    if ($media_status == 'not_found') {
                        Storage::disk('temp')->delete($file);
                    }
                }
            }
            Log::info('ダウンロードjsonチェック終了****************');
        }

        $retry_files = Storage::disk('temp')->files('retry_video_request');
        if (count($retry_files)) {
            Log::info('リトライダウンロードjsonチェック開始****************');
            foreach ($retry_files as $file) {
                $file_content = Storage::disk('temp')->get($file);
                if ($file_content != null) {
                    $file_content = json_decode($file_content);
                }
                if (isset($file_content->request_id) && $file_content->request_id > 0) {
                    $safie_service = new SafieApiService($file_content->contract_no);
                    $media_status = $safie_service->getMediaFileStatus($file_content->device_id, $file_content->request_id);
                    if ($media_status != null && isset($media_status['state'])) {
                        Log::info($media_status);
                        if ($media_status['state'] !== 'AVAILABLE') {
                            continue;
                        }
                        if ($media_status['state'] == 'AVAILABLE') {
                            $video_data = $safie_service->downloadMediaFile($media_status['url']);
                            if ($video_data == null) {
                                continue;
                            }
                            if ($video_data == 'not_found') {
                                Storage::disk('temp')->delete($file);
                                continue;
                            }
                            $thumb_image = $safie_service->getDeviceImage($file_content->device_id, $file_content->starttime_format_for_image);
                            $file_name = date('YmdHis', strtotime($file_content->starttime)).'.mp4';

                            $type = $file_content->type;
                            $folder_name = 'danger';
                            switch ($type) {
                                case 'danger_area':
                                    $folder_name = 'danger';
                                    $table_name = 'danger_area_detections';
                                    break;
                                case 'shelf':
                                    $folder_name = 'shelf';
                                    $table_name = 'shelf_detections';
                                    break;
                                case 'pit':
                                    $folder_name = 'pit';
                                    $table_name = 'pit_detections';
                                    break;
                                case 'thief':
                                    $folder_name = 'thief';
                                    $table_name = 'thief_detections';
                                    break;
                            }

                            Storage::disk('video')->put($folder_name.'\\'.$file_content->device_id.'\\'.$file_name, $video_data);
                            $video_file_path = $folder_name.'/'.$file_content->device_id.'/'.$file_name;
                            $thumb_img_path = null;
                            if ($thumb_image != null) {
                                Storage::disk('thumb')->put($folder_name.'\\'.$file_content->device_id.'\\'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg', $thumb_image);
                                $thumb_img_path = $folder_name.'/'.$file_content->device_id.'/'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg';
                            }
                            DB::table($table_name)->where('id', $file_content->table_id)
                                ->update([
                                    'video_file_path' => $video_file_path,
                                    'thumb_img_path' => $thumb_img_path,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                            $safie_service->deleteMediaFile($file_content->device_id, $file_content->request_id);
                            Storage::disk('temp')->delete($file);
                        }
                    }
                    if ($media_status == 'not_found') {
                        Storage::disk('temp')->delete($file);
                    }
                }
            }
            Log::info('リトライダウンロードjsonチェック終了****************');
        }
        Log::info('Ending check detection json file exist****************');

        $camera_start_on_time = config('const.camera_start_time');
        $camera_end_off_time = config('const.camera_end_time');
        $request_interval = config('const.request_interval');
        $detection_video_length = config('const.detection_video_length');
        $cur_time_object = new \DateTime();
        $cur_time_object->setTimezone(new \DateTimeZone('+0900')); //GMT
        $now = $cur_time_object->format('Y-m-d H:i:s');
        $now_date = $cur_time_object->format('Y-m-d');

        if (!(strtotime($now_date.' '.$camera_start_on_time) + ($request_interval + 3) * 60 <= strtotime($now) && ($request_interval + 3) * 60 + strtotime($now_date.' '.$camera_end_off_time) >= strtotime($now))) {
            $try_limit = 0;
            $re_request_files = Storage::disk('temp')->files('retry_ai_request');
            if (count($re_request_files) > 0) {
                Log::info('失敗したBI->AIリクエストjsonチェック開始!!!!!!!');
                foreach ($re_request_files as $file) {
                    if ($try_limit > 6) {
                        continue;
                    }
                    $file_content = Storage::disk('temp')->get($file);
                    if ($file_content != null) {
                        $file_content = json_decode($file_content);
                    }
                    $url = $file_content->url;
                    $params = $file_content->params;
                    $header = [
                        'Content-Type: application/json',
                    ];
                    $ai_res = $this->sendPostApi($url, $header, $params, 'json');
                    if ($ai_res == 200) {
                        Storage::disk('temp')->delete($file);
                        ++$try_limit;
                    } else {
                        if ($ai_res == 503 || $ai_res == 530) {
                            Log::info('解析を止める用API（BI→AI）開始--------');
                            $stop_url = config('const.ai_server').'stop-analysis';
                            $stop_params = [];
                            $stop_params['camera_info'] = [];
                            $stop_params['camera_info']['camera_id'] = $file_content->device_id;
                            $stop_params['camera_info']['rule_name'] = $file_content->type == 'danger' ? 'danger_zone' : 'ee_count';
                            $stop_params['priority'] = 1;
                            $stop_params['request_type'] = 2;
                            $this->sendPostApi($stop_url, $header, $stop_params, 'json');
                            Log::info('解析を止める用API（BI→AI）中止--------');
                        }
                    }
                }
                Log::info('失敗したBI->AIリクエストjsonチェック終了!!!!!!!');
            }
        }

        //check failed detection data------------------------------------------------
        $camera_data = [];
        // if (!(strtotime($now_date.' '.$camera_start_on_time) <= strtotime($now) && strtotime($now_date.' '.$camera_end_off_time) >= strtotime($now))) {
        $data = DangerAreaDetection::query()->where('video_file_path', '')->orderBy('starttime')->get()->all();
        foreach ($data as $item) {
            if (isset($camera_data[$item->camera_id])) {
                continue;
            }
            Log::info('AI->BI用失敗したメディアファイル再作成要求------------危険エリア侵入検知');
            $camera_item = CameraService::getCameraInfoById($item->camera_id);
            if ($camera_item != null) {
                $camera_data[$item->camera_id] = $camera_item;
            }

            $start_datetime_object = new \DateTime($item->starttime, new \DateTimeZone('GMT+9'));
            $thumb_datetime_object = clone $start_datetime_object;
            $start_datetime_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
            $end_datetime_object = new \DateTime($item->endtime, new \DateTimeZone('GMT+9'));
            Log::info('retry start = '.$start_datetime_object->format('c'));
            Log::info('retry end = '.$end_datetime_object->format('c'));
            $safie_service = new SafieApiService($camera_data[$item->camera_id]->contract_no);
            $request_id = $safie_service->makeMediaFile($camera_data[$item->camera_id]->camera_id, $start_datetime_object->format('c'), $end_datetime_object->format('c'), '危険エリア侵入検知', $camera_data[$item->camera_id]->reopened_at);
            if ((int) $request_id > 0) {
                $temp_save_data = [
                        'starttime' => $thumb_datetime_object->format('Y-m-d H:i:s'),
                        'device_id' => $camera_data[$item->camera_id]->camera_id,
                        'camera_id' => $camera_data[$item->camera_id]->id,
                        'contract_no' => $camera_data[$item->camera_id]->contract_no,
                        'request_id' => $request_id,
                        'table_id' => $item->id,
                        'type' => 'danger_area',
                        'starttime_format_for_image' => $thumb_datetime_object->format('Y-m-d\TH:i:sO'),
                    ];
                Storage::disk('temp')->put('retry_video_request\\'.$camera_data[$item->camera_id]->camera_id.'_danger_area_'.$item->id.'.json', json_encode($temp_save_data));
            } else {
                if ($request_id == 'http_code_404') {
                    DB::table('danger_area_detections')->where('id', $item->id)->delete();
                }
            }
        }

        $data = PitDetection::query()->where('video_file_path', '')->orderBy('starttime')->get()->all();
        foreach ($data as $item) {
            if (isset($camera_data[$item->camera_id])) {
                continue;
            }
            Log::info('AI->BI用失敗したメディアファイル再作成要求------------ピット入退場検知');
            $camera_item = CameraService::getCameraInfoById($item->camera_id);
            if ($camera_item != null) {
                $camera_data[$item->camera_id] = $camera_item;
            }

            $start_datetime_object = new \DateTime($item->starttime, new \DateTimeZone('GMT+9'));
            $thumb_datetime_object = clone $start_datetime_object;
            $start_datetime_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
            $end_datetime_object = new \DateTime($item->endtime, new \DateTimeZone('GMT+9'));
            Log::info('retry start = '.$start_datetime_object->format('c'));
            Log::info('retry end = '.$end_datetime_object->format('c'));
            $safie_service = new SafieApiService($camera_data[$item->camera_id]->contract_no);
            $request_id = $safie_service->makeMediaFile($camera_data[$item->camera_id]->camera_id, $start_datetime_object->format('c'), $end_datetime_object->format('c'), 'ピット入退場検知', $camera_data[$item->camera_id]->reopened_at);
            if ((int) $request_id > 0) {
                $temp_save_data = [
                        'starttime' => $thumb_datetime_object->format('Y-m-d H:i:s'),
                        'device_id' => $camera_data[$item->camera_id]->camera_id,
                        'camera_id' => $camera_data[$item->camera_id]->id,
                        'contract_no' => $camera_data[$item->camera_id]->contract_no,
                        'request_id' => $request_id,
                        'table_id' => $item->id,
                        'type' => 'pit',
                        'starttime_format_for_image' => $thumb_datetime_object->format('Y-m-d\TH:i:sO'),
                    ];
                Storage::disk('temp')->put('retry_video_request\\'.$camera_data[$item->camera_id]->camera_id.'_pit_'.$item->id.'.json', json_encode($temp_save_data));
            } else {
                if ($request_id == 'http_code_404') {
                    DB::table('pit_detections')->where('id', $item->id)->delete();
                }
            }
        }
        $data = ShelfDetection::query()->where('video_file_path', '')->orderBy('starttime')->get()->all();
        foreach ($data as $item) {
            if (isset($camera_data[$item->camera_id])) {
                continue;
            }
            Log::info('AI->BI用失敗したメディアファイル再作成要求------------棚乱れ');
            $camera_item = CameraService::getCameraInfoById($item->camera_id);
            if ($camera_item != null) {
                $camera_data[$item->camera_id] = $camera_item;
            }

            $start_datetime_object = new \DateTime($item->starttime, new \DateTimeZone('GMT+9'));
            $thumb_datetime_object = clone $start_datetime_object;
            $start_datetime_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
            $end_datetime_object = new \DateTime($item->endtime, new \DateTimeZone('GMT+9'));
            Log::info('retry start = '.$start_datetime_object->format('c'));
            Log::info('retry end = '.$end_datetime_object->format('c'));
            $safie_service = new SafieApiService($camera_data[$item->camera_id]->contract_no);
            $request_id = $safie_service->makeMediaFile($camera_data[$item->camera_id]->camera_id, $start_datetime_object->format('c'), $end_datetime_object->format('c'), '棚乱れ検知', $camera_data[$item->camera_id]->reopened_at);
            if ((int) $request_id > 0) {
                $temp_save_data = [
                        'starttime' => $thumb_datetime_object->format('Y-m-d H:i:s'),
                        'device_id' => $camera_data[$item->camera_id]->camera_id,
                        'camera_id' => $camera_data[$item->camera_id]->id,
                        'contract_no' => $camera_data[$item->camera_id]->contract_no,
                        'request_id' => $request_id,
                        'table_id' => $item->id,
                        'type' => 'shelf',
                        'starttime_format_for_image' => $thumb_datetime_object->format('Y-m-d\TH:i:sO'),
                    ];
                Storage::disk('temp')->put('retry_video_request\\'.$camera_data[$item->camera_id]->camera_id.'_shelf_'.$item->id.'.json', json_encode($temp_save_data));
            } else {
                if ($request_id == 'http_code_404') {
                    DB::table('shelf_detections')->where('id', $item->id)->delete();
                }
            }
        }
        $data = ThiefDetection::query()->where('video_file_path', '')->orderBy('starttime')->get()->all();
        foreach ($data as $item) {
            if (isset($camera_data[$item->camera_id])) {
                continue;
            }
            Log::info('AI->BI用失敗したメディアファイル再作成要求------------大量盗難検知');
            $camera_item = CameraService::getCameraInfoById($item->camera_id);
            if ($camera_item != null) {
                $camera_data[$item->camera_id] = $camera_item;
            }

            $start_datetime_object = new \DateTime($item->starttime, new \DateTimeZone('GMT+9'));
            $thumb_datetime_object = clone $start_datetime_object;
            $start_datetime_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
            $end_datetime_object = new \DateTime($item->endtime, new \DateTimeZone('GMT+9'));
            Log::info('retry start = '.$start_datetime_object->format('c'));
            Log::info('retry end = '.$end_datetime_object->format('c'));
            $safie_service = new SafieApiService($camera_data[$item->camera_id]->contract_no);
            $request_id = $safie_service->makeMediaFile($camera_data[$item->camera_id]->camera_id, $start_datetime_object->format('c'), $end_datetime_object->format('c'), '大量盗難検知', $camera_data[$item->camera_id]->reopened_at);
            if ((int) $request_id > 0) {
                $temp_save_data = [
                        'starttime' => $thumb_datetime_object->format('Y-m-d H:i:s'),
                        'device_id' => $camera_data[$item->camera_id]->camera_id,
                        'camera_id' => $camera_data[$item->camera_id]->id,
                        'contract_no' => $camera_data[$item->camera_id]->contract_no,
                        'request_id' => $request_id,
                        'table_id' => $item->id,
                        'type' => 'thief',
                        'starttime_format_for_image' => $thumb_datetime_object->format('Y-m-d\TH:i:sO'),
                    ];
                Storage::disk('temp')->put('retry_video_request\\'.$camera_data[$item->camera_id]->camera_id.'_thief_'.$item->id.'.json', json_encode($temp_save_data));
            } else {
                if ($request_id == 'http_code_404') {
                    DB::table('thief_detections')->where('id', $item->id)->delete();
                }
            }
        }
        // }

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
