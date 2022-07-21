<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\SafieApiService;
use App\Service\DangerService;
use App\Service\ShelfService;
use App\Service\PitService;
use App\Service\ThiefService;
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
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return ['error' => 'デバイスがありません。'];
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return ['error' => '解析ルールがありません。'];
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return ['error' => '解析ルールIDがありません。'];
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return ['error' => '検知開始日時がありません。'];
        }
        $rule_id = $request['analyze_result']['rect_id'];
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
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
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

    public function saveShelfDetection(Request $request)
    {
        Log::info('Shelf Detection Receive Start****************');
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return ['error' => 'デバイスがありません。'];
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return ['error' => '解析ルールがありません。'];
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return ['error' => '解析ルールIDがありません。'];
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return ['error' => '検知開始日時がありません。'];
        }
        $rule_id = $request['analyze_result']['rect_id'];
        Log::info('rule id = '.$rule_id);
        $shelf_service = new ShelfService();
        $camera_data = $shelf_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return ['error' => 'デバイスがありません。'];
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return ['error' => 'デバイスがありません。'];
        }
        $request_interval = config('const.request_interval');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
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
                'type' => 'shelf',
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('Finish Shelf Detection****************');

            return ['success' => '送信成功'];
        } else {
            Log::info('Finish Shelf Detection****************');

            return ['success' => '送信失敗'];
        }
    }

    public function savePitDetection(Request $request)
    {
        Log::info('Pit Detection Receive Start****************');
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return ['error' => 'デバイスがありません。'];
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return ['error' => '解析ルールがありません。'];
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return ['error' => '解析ルールIDがありません。'];
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return ['error' => '検知開始日時がありません。'];
        }
        $rule_id = $request['analyze_result']['rect_id'];
        Log::info('rule id = '.$rule_id);
        $pit_service = new PitService();
        $camera_data = $pit_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return ['error' => 'デバイスがありません。'];
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return ['error' => 'デバイスがありません。'];
        }
        $nb_entry = 0;
        $nb_exit = 0;
        if (isset($request['analyze_result']['nb_entry']) && $request['analyze_result']['nb_entry'] > 0) {
            $nb_entry = $request['analyze_result']['nb_entry'];
        }
        if (isset($request['analyze_result']['nb_exit']) && $request['analyze_result']['nb_exit'] > 0) {
            $nb_exit = $request['analyze_result']['nb_exit'];
        }
        $request_interval = config('const.request_interval');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
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
                'type' => 'pit',
                'nb_entry' => $nb_entry,
                'nb_exit' => $nb_exit,
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('Finish pit Detection****************');

            return ['success' => '送信成功'];
        } else {
            Log::info('Finish pit Detection****************');

            return ['success' => '送信失敗'];
        }
    }

    public function saveThiefDetection(Request $request)
    {
        Log::info('Thief Detection Receive Start****************');
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return ['error' => 'デバイスがありません。'];
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return ['error' => '解析ルールがありません。'];
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return ['error' => '解析ルールIDがありません。'];
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return ['error' => '検知開始日時がありません。'];
        }

        $rule_id = $request['analyze_result']['rect_id'];
        Log::info('rule id = '.$rule_id);
        $thief_service = new ThiefService();
        $camera_data = $thief_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return ['error' => 'デバイスがありません。'];
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return ['error' => 'デバイスがありません。'];
        }
        $request_interval = config('const.request_interval');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
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
                'type' => 'thief',
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('Finish thief Detection****************');

            return ['success' => '送信成功'];
        } else {
            Log::info('Finish thief Detection****************');

            return ['success' => '送信失敗'];
        }
    }
}
