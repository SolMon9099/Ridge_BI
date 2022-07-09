<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\SafieApiService;
use App\Service\DangerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DetectionController extends Controller
{
    public function __construct()
    {
    }

    public function saveDangerDetection(Request $request)
    {
        Log::info('Danger Detection Receive Start****************');
        if (!(isset($request['analyze_rule_id']) && $request['analyze_rule_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return ['error' => '解析ルールIDがありません。'];
        }
        if (!(isset($request['detect_start_date']) && $request['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return ['error' => '検知開始日時がありません。'];
        }
        $rule_id = $request['analyze_rule_id'];
        Log::info('rule id = '.$rule_id);
        $danger_service = new DangerService();
        $camera_data = $danger_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return ['error' => 'デバイスがありません。'];
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return ['error' => 'デバイスがありません。'];
        }
        $request_interval = config('const.request_interval');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['detect_start_date']));
        Log::info('start datetime = '.$start_datetime);

        $time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime, new \DateTimeZone('+0900'));
        $record_start_time = $time_object->format('c');
        $record_end_time_object = clone $time_object;
        if ((isset($request['detect_end_date']) && $request['detect_end_date'] != '')) {
            $end_datetime = date('Y-m-d H:i:s', strtotime($request['detect_end_date']));
            $record_end_time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $end_datetime, new \DateTimeZone('+0900'));
        } else {
            $record_end_time_object->add(new \DateInterval('PT'.(string) $request_interval.'M'));
        }
        $record_end_time = $record_end_time_object->format('c');
        Log::info('record startdatetime = '.$record_start_time);
        Log::info('record enddatetime = '.$record_end_time);

        $safie_service = new SafieApiService($camera_data->contract_no);
        $request_id = $safie_service->makeMediaFile($camera_data->camera_id, $record_start_time, $record_end_time);
        Log::info('request_id = '.$request_id);
        if ($request_id > 0) {
            $temp_save_data = [
                'request_id' => $request_id,
                'starttime' => $start_datetime,
                'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                'camera_no' => $camera_data->camera_id,
                'camera_id' => $camera_data->id,
                'contract_no' => $camera_data->contract_no,
                'rule_id' => $rule_id,
                'type' => 'danger_area',
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('Finish Danger Detection****************');
            return ['success' => '送信成功'];
        } else {
            Log::info('Finish Danger Detection****************');
            return ['success' => '送信失敗'];
        }
    }
}
