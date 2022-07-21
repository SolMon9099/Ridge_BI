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
            foreach ($all_files as $file) {
                $file_content = Storage::disk('temp')->get($file);
                if ($file_content != null) {
                    $file_content = json_decode($file_content);
                }
                if (isset($file_content->request_id) && $file_content->request_id > 0) {
                    $safie_service = new SafieApiService($file_content->contract_no);
                    $media_status = $safie_service->getMediaFileStatus($file_content->camera_no, $file_content->request_id);
                    if ($media_status != null) {
                        Log::info($media_status);
                        if ($media_status['state'] !== 'AVAILABLE') {
                            continue;
                        }
                        if ($media_status['state'] == 'AVAILABLE') {
                            $video_data = $safie_service->downloadMediaFile($media_status['url']);
                            if ($video_data == null) {
                                continue;
                            }
                            Log::info('video ok~~~~~~~');
                            $thumb_image = $safie_service->getDeviceImage($file_content->camera_no, $file_content->starttime_format_for_image);
                            $file_name = date('YmdHis', strtotime($file_content->starttime)).'.mp4';

                            $type = $file_content->type;
                            $folder_name = 'danger';
                            switch ($type) {
                                case 'danger_area':
                                    $folder_name = 'danger';
                                    $detection_model = new DangerAreaDetection();
                                    break;
                                case 'shelf':
                                    $folder_name = 'shelf';
                                    $detection_model = new ShelfDetection();
                                    break;
                                case 'pit':
                                    $folder_name = 'pit';
                                    $detection_model = new PitDetection();
                                    if (isset($file_content->nb_exit)) {
                                        $detection_model->nb_exit = $file_content->nb_exit;
                                    }
                                    if (isset($file_content->nb_entry)) {
                                        $detection_model->nb_entry = $file_content->nb_entry;
                                    }
                                    break;
                                case 'thief':
                                    $folder_name = 'thief';
                                    $detection_model = new ThiefDetection();
                                    break;
                            }
                            Storage::disk('video')->put($folder_name.'\\'.$file_content->camera_no.'\\'.$file_name, $video_data);

                            $detection_model->camera_id = $file_content->camera_id;
                            $detection_model->rule_id = $file_content->rule_id;
                            $detection_model->video_file_path = $folder_name.'/'.$file_content->camera_no.'/'.$file_name;
                            $detection_model->starttime = $file_content->starttime;
                            $detection_model->endtime = $file_content->endtime;
                            if ($thumb_image != null) {
                                Storage::disk('thumb')->put($folder_name.'\\'.$file_content->camera_no.'\\'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg', $thumb_image);
                                $detection_model->thumb_img_path = $folder_name.'/'.$file_content->camera_no.'/'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg';
                            }
                            $detection_model->save();
                            $safie_service->deleteMediaFile($file_content->camera_no, $file_content->request_id);
                            Storage::disk('temp')->delete($file);
                        }
                    }
                }
            }
        }
        Log::info('Ending check detection json file exist****************');

        return 0;
    }
}
