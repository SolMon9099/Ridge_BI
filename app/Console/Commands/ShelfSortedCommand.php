<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
// use App\Models\Camera;
use App\Models\ShelfDetectionRule;

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
        // $danger_file = Storage::disk('video')->get('hanger_2022-10-12_16-00-00.mp4');
        // $danger_file_1 = Storage::disk('video')->get('hanger_2022-10-12_17-00-00.mp4');
        // $danger_file_2 = Storage::disk('video')->get('hanger_2022-10-12_18-00-00.mp4');
        // $danger_file_3 = Storage::disk('video')->get('hanger_2022-10-12_19-00-00.mp4');
        // $danger_file_4 = Storage::disk('video')->get('hanger_2022-10-12_20-00-00.mp4');
        // $shelf_file = Storage::disk('video')->get('shelf_2022-09-22_16-00-00.mp4');
        // $thief_file = Storage::disk('video')->get('hanger_2022-09-22_15-00-00.mp4');
        // Storage::disk('s3')->put('test_hanger/20220922/2022-09-22_15-00-00.mp4', $thief_file);
        // Storage::disk('s3')->put('test_hanger/hanger_2022-10-12_16-00-00.mp4', $danger_file);
        // Storage::disk('s3')->put('test_hanger/hanger_2022-10-12_17-00-00.mp4', $danger_file_1);
        // Storage::disk('s3')->put('test_hanger/hanger_2022-10-12_18-00-00.mp4', $danger_file_2);
        // Storage::disk('s3')->put('test_hanger/hanger_2022-10-12_19-00-00.mp4', $danger_file_3);
        // Storage::disk('s3')->put('test_hanger/hanger_2022-10-12_20-00-00.mp4', $danger_file_4);
        // Storage::disk('s3')->put('test_shelf/20220922/2022-09-22_15-00-00.mp4', $shelf_file);

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

        return 0;
    }
}
