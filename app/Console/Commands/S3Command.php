<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use App\Models\S3VideoHistory;
use Illuminate\Support\Facades\Storage;

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
        if (strtotime($camera_start_on_time) <= strtotime($now) && strtotime($camera_end_off_time) >= strtotime($now)) {
            $safie_service = new SafieApiService();
            $record_start_time_object = clone $cur_time_object;
            $record_start_time_object->sub(new \DateInterval('PT'.(string) 2 * $request_interval.'M'));
            $record_start_time = $record_start_time_object->format('c');
            $record_end_time_object = clone $cur_time_object;
            $record_end_time_object->sub(new \DateInterval('PT'.(string) $request_interval.'M'));
            $record_end_time = $record_end_time_object->format('c');

            //メディアファイル作成要求一覧取得・S3に保存--------------------------------
            $this->saveMedia();
            //----------------------------------------------------------------------

            //メディアファイル作成要求--------------------------------
            $request_id = $safie_service->makeMediaFile(null, $record_start_time, $record_end_time);
            if ($request_id > 0) {
                $this->createS3History($request_id, $record_start_time_object, $record_end_time_object);
            }
            //--------------------------------------------------------
        }

        return 0;
    }

    public function createS3History($request_id, $record_start_time_object, $record_end_time_object, $device_id = null)
    {
        $record = new S3VideoHistory();
        $record->request_id = $request_id;
        $record->device_id = $device_id == null ? 'FvY6rnGWP12obPgFUj0a' : $device_id;
        $record->start_time = $record_start_time_object->format('Y-m-d H:i:s');
        $record->end_time = $record_end_time_object->format('Y-m-d H:i:s');
        $record->save();
    }

    public function saveMedia($device_id = null)
    {
        $device_id = $device_id == null ? 'FvY6rnGWP12obPgFUj0a' : $device_id;
        $data = S3VideoHistory::query()->where('device_id', $device_id)
            ->where('status', '!=', 2)
            ->orderByDesc('updated_at')->limit(10)->get();

        $safie_service = new SafieApiService();
        foreach ($data as $item) {
            $media_status = $safie_service->getMediaFileStatus($device_id, $item->request_id);
            if ($media_status != null) {
                if ($media_status['state'] == 'AVAILABLE') {
                    if ($item->status == 0) {
                        $item->status = 1;
                        $item->save();
                    }
                    $video_data = $safie_service->downloadMediaFile($media_status['url']);

                    if ($video_data != null) {
                        $start_time = $item->start_time;
                        $end_time = $item->end_time;
                        $start_date = date('Ymd', strtotime($start_time));
                        $file_name = date('YmdHis', strtotime($start_time)).'_'.date('YmdHis', strtotime($end_time));
                        Storage::disk('s3')->put($device_id.'\\'.$start_date.'\\'.$file_name, $video_data);
                        // Storage::disk('s3')->delete('FvY6rnGWP12obPgFUj0a/20220622071500__.mp4');
                        $item->status = 2;
                        $item->file_path = $device_id.'/'.$start_date.'/'.$file_name;
                        $item->save();
                    }
                }
            }
        }
    }
}