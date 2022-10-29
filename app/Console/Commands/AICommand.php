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
        // $all_503_files = Storage::disk('temp')->files('media_request_503');
        // if (count($all_503_files) > 0) {
        //     Log::info('503jsonチェック開始****************');
        //     foreach ($all_503_files as $file) {
        //         $file_content = Storage::disk('temp')->get($file);
        //         if ($file_content != null) {
        //             $file_content = json_decode($file_content);
        //         }
        //         if (isset($file_content->device_id) && $file_content->device_id != '') {
        //             $type = $file_content->type;
        //             $resource_name = '危険エリア侵入検知';
        //             switch ($type) {
        //                 case 'danger_area':
        //                     $resource_name = '危険エリア侵入検知';
        //                     break;
        //                 case 'pit':
        //                     $resource_name = 'ピット入退場検知';
        //                     break;
        //                 case 'shelf':
        //                     $resource_name = '棚乱れ検知';
        //                     break;
        //                 case 'thief':
        //                     $resource_name = '大量盗難検知';
        //                     break;
        //             }

        //             $safie_service = new SafieApiService($file_content->contract_no);
        //             $request_id = $safie_service->makeMediaFile($file_content->device_id, $file_content->record_start_time, $file_content->record_end_time, $resource_name);
        //             Log::info('503 retry http code = '.$request_id);
        //             if ($request_id > 0) {
        //                 $file_content->request_id = $request_id;
        //                 Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode((array) $file_content));
        //             } else {
        //                 if ($request_id != null && str_replace('http_code_', '', $request_id) == 503) {
        //                     continue;
        //                 }
        //             }
        //             Storage::disk('temp')->delete($file);
        //         }
        //     }
        //     Log::info('503jsonチェック終了****************');
        // }

        Log::info('Ending check detection json file exist****************');

        return 0;
    }
}
