<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\DangerAreaDetection;

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
                            Storage::disk('video')->put('danger'.'\\'.$file_content->camera_no.'\\'.$file_name, $video_data);
                            $danger_detection = new DangerAreaDetection();
                            $danger_detection->camera_id = $file_content->camera_id;
                            $danger_detection->rule_id = $file_content->rule_id;
                            $danger_detection->video_file_path = 'danger'.'/'.$file_content->camera_no.'/'.$file_name;
                            $danger_detection->starttime = $file_content->starttime;
                            $danger_detection->endtime = $file_content->endtime;
                            if ($thumb_image != null) {
                                Storage::disk('thumb')->put('danger'.'\\'.$file_content->camera_no.'\\'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg', $thumb_image);
                                $danger_detection->thumb_img_path = 'danger'.'/'.$file_content->camera_no.'/'.date('YmdHis', strtotime($file_content->starttime)).'.jpeg';
                            }
                            $danger_detection->save();
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
