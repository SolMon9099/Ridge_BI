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
use App\Models\Heatmap;
use App\Models\DangerAreaDetection;
use App\Models\PitDetection;
use App\Models\VcDetection;
use Illuminate\Support\Facades\DB;

class DetectionController extends Controller
{
    public function __construct()
    {
    }

    public function saveDangerDetection(Request $request)
    {
        Log::info('危険エリア侵入検知解析結果送受信API（AI→BI）開始');
        Log::info('パラメータ');
        Log::info($request);
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return response()->json(['error' => 'デバイスがありません。'], 400);
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return response()->json(['error' => '解析ルールがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return response()->json(['error' => '解析ルールIDがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return response()->json(['error' => '検知開始日時がありません。'], 400);
        }
        if (!(isset($request['analyze_result']['action_id']) && $request['analyze_result']['action_id'] > 0)) {
            Log::info('アクションデータがありません。-------------');

            return response()->json(['error' => 'アクションデータがありません。'], 400);
        }
        $rule_id = $request['analyze_result']['rect_id'];
        Log::info('rule id = '.$rule_id);
        $detection_action_id = $request['analyze_result']['action_id'];
        $danger_service = new DangerService();
        $camera_data = $danger_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        //send alert mails---------------
        $this->sendAlertMail($camera_data->serial_no, 'danger');
        //-------------------------------
        $detection_video_length = config('const.detection_video_length');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
        $exist_record = DangerAreaDetection::query()->where('camera_id', $camera_data->id)
            ->where('starttime', $start_datetime)
            ->get()->first();
        if ($exist_record != null) {
            DangerAreaDetection::query()->where('id', $exist_record->id)
                ->update([
                    'rule_id' => $rule_id,
                    'detection_action_id' => $detection_action_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            return response()->json(['success' => '送信成功'], 200);
        }

        Log::info('start datetime = '.$start_datetime);

        $time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime, new \DateTimeZone('+0900'));
        $record_end_time_object = clone $time_object;
        if ((isset($request['detect_end_date']) && $request['detect_end_date'] != '')) {
            $end_datetime = date('Y-m-d H:i:s', strtotime($request['detect_end_date']));
            $record_end_time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $end_datetime, new \DateTimeZone('+0900'));
        } else {
            $record_end_time_object->add(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        }
        $record_end_time = $record_end_time_object->format('c');
        $record_start_time_object = clone $time_object;
        $record_start_time_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        $record_start_time = $record_start_time_object->format('c');
        Log::info('record startdatetime = '.$record_start_time);
        Log::info('record enddatetime = '.$record_end_time);

        $safie_service = new SafieApiService($camera_data->contract_no);
        $request_id = null;
        if ($camera_data->is_enabled == 1) {
            $request_id = $safie_service->makeMediaFile($camera_data->camera_id, $record_start_time, $record_end_time, '危険エリア侵入検知', $camera_data->reopened_at);
        } else {
            Log::info('動画取得が中止されたカメラ '.$camera_data->camera_id);
        }

        Log::info('request_id = '.$request_id);
        if ($request_id > 0) {
            $temp_save_data = [
                    'request_id' => $request_id,
                    'starttime' => $start_datetime,
                    'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                    'device_id' => $camera_data->camera_id,
                    'camera_id' => $camera_data->id,
                    'contract_no' => $camera_data->contract_no,
                    'rule_id' => $rule_id,
                    'detection_action_id' => $detection_action_id,
                    'type' => 'danger_area',
                    'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
                ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('危険エリア侵入検知解析結果送受信API（AI→BI）終了');

            return response()->json(['success' => '送信成功'], 200);
        } else {
            if ($request_id != null) {
                $http_code = str_replace('http_code_', '', $request_id);
                // if ($http_code == 503) {
                //     Log::info('危険エリア侵入：メディアファイル 作成要求失敗ー503');
                //     Log::info('メディアファイル 作成要求臨時保存');
                //     $temp_save_data = [
                //         'record_start_time' => $record_start_time,
                //         'record_end_time' => $record_end_time,
                //         'starttime' => $start_datetime,
                //         'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                //         'device_id' => $camera_data->camera_id,
                //         'camera_id' => $camera_data->id,
                //         'contract_no' => $camera_data->contract_no,
                //         'rule_id' => $rule_id,
                //         'detection_action_id' => $detection_action_id,
                //         'type' => 'danger_area',
                //         'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
                //     ];
                //     Storage::disk('temp')->put('media_request_503\\'.$camera_data->camera_id.'_danger_area_'.$record_start_time.'.json', json_encode($temp_save_data));
                // }
            }
            $detection_model = new DangerAreaDetection();
            $detection_model->detection_action_id = $detection_action_id;
            $detection_model->camera_id = $camera_data->id;
            $detection_model->rule_id = $rule_id;
            $detection_model->video_file_path = '';
            $detection_model->starttime = $start_datetime;
            $detection_model->endtime = $record_end_time_object->format('Y-m-d H:i:s');
            $detection_model->thumb_img_path = '';
            $detection_model->save();
            Log::info('危険エリア侵入検知解析結果送受信API（AI→BI）終了');

            return response()->json(['error' => 'カメラメディアファイル作成失敗'], 200);
        }
    }

    public function saveShelfDetection(Request $request)
    {
        Log::info('棚乱れ検知ルール通知解析結果送受信API（AI→BI）開始');
        Log::info('パラメータ');
        Log::info($request);
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return response()->json(['error' => 'デバイスがありません。'], 400);
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return response()->json(['error' => '解析ルールがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return response()->json(['error' => '解析ルールIDがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return response()->json(['error' => '検知開始日時がありません。'], 400);
        }
        $rule_id = $request['analyze_result']['rect_id'];
        Log::info('rule id = '.$rule_id);
        $shelf_service = new ShelfService();
        $camera_data = $shelf_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        $detection_video_length = config('const.detection_video_length');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
        Log::info('start datetime = '.$start_datetime);

        $time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime, new \DateTimeZone('+0900'));
        $record_end_time_object = clone $time_object;
        if ((isset($request['detect_end_date']) && $request['detect_end_date'] != '')) {
            $end_datetime = date('Y-m-d H:i:s', strtotime($request['detect_end_date']));
            $record_end_time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $end_datetime, new \DateTimeZone('+0900'));
        } else {
            $record_end_time_object->add(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        }
        $record_end_time = $record_end_time_object->format('c');
        $record_start_time_object = clone $time_object;
        $record_start_time_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        $record_start_time = $record_start_time_object->format('c');
        Log::info('record startdatetime = '.$record_start_time);
        Log::info('record enddatetime = '.$record_end_time);

        $safie_service = new SafieApiService($camera_data->contract_no);
        $request_id = null;
        if ($camera_data->is_enabled == 1) {
            $request_id = $safie_service->makeMediaFile($camera_data->camera_id, $record_start_time, $record_end_time, '棚乱れ検知', $camera_data->reopened_at);
        } else {
            Log::info('動画取得が中止されたカメラ '.$camera_data->camera_id);
        }
        Log::info('request_id = '.$request_id);
        if ($request_id > 0) {
            $temp_save_data = [
                'request_id' => $request_id,
                'starttime' => $start_datetime,
                'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                'device_id' => $camera_data->camera_id,
                'camera_id' => $camera_data->id,
                'contract_no' => $camera_data->contract_no,
                'rule_id' => $rule_id,
                'type' => 'shelf',
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('棚乱れ検知ルール通知解析結果送受信API（AI→BI）終了');

            return response()->json(['success' => '送信成功'], 200);
        } else {
            if ($request_id != null) {
                $http_code = str_replace('http_code_', '', $request_id);
                // if ($http_code == 503) {
                //     Log::info('棚乱れ：メディアファイル 作成要求失敗ー503');
                //     Log::info('メディアファイル 作成要求臨時保存');
                //     $temp_save_data = [
                //         'record_start_time' => $record_start_time,
                //         'record_end_time' => $record_end_time,
                //         'starttime' => $start_datetime,
                //         'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                //         'device_id' => $camera_data->camera_id,
                //         'camera_id' => $camera_data->id,
                //         'contract_no' => $camera_data->contract_no,
                //         'rule_id' => $rule_id,
                //         'type' => 'shelf',
                //         'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
                //     ];
                //     Storage::disk('temp')->put('media_request_503\\'.$camera_data->camera_id.'_shelf_'.$record_start_time.'.json', json_encode($temp_save_data));
                // }
            }
            Log::info('棚乱れ検知ルール通知解析結果送受信API（AI→BI）終了');

            return response()->json(['error' => 'カメラメディアファイル作成失敗'], 200);
        }
    }

    public function savePitDetection(Request $request)
    {
        Log::info('ピット入退場解析結果送受信API（AI→BI）開始');
        Log::info('パラメータ');
        Log::info($request);
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return response()->json(['error' => 'デバイスがありません。'], 400);
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return response()->json(['error' => '解析ルールがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return response()->json(['error' => '解析ルールIDがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return response()->json(['error' => '検知開始日時がありません。'], 400);
        }
        $rule_id = $request['analyze_result']['rect_id'];
        Log::info('rule id = '.$rule_id);
        $pit_service = new PitService();
        $camera_data = $pit_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        $nb_entry = 0;
        $nb_exit = 0;
        if (isset($request['analyze_result']['nb_entry']) && $request['analyze_result']['nb_entry'] > 0) {
            $nb_entry = $request['analyze_result']['nb_entry'];
        }
        if (isset($request['analyze_result']['nb_exit']) && $request['analyze_result']['nb_exit'] > 0) {
            $nb_exit = $request['analyze_result']['nb_exit'];
        }
        //send alert mails---------------
        $this->sendAlertMail($camera_data->serial_no, 'pit');
        //-------------------------------
        $detection_video_length = config('const.detection_video_length');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
        Log::info('start datetime = '.$start_datetime);

        $exist_records = PitDetection::query()->where('camera_id', $camera_data->id)
            ->where('starttime', $start_datetime)
            ->get()->all();
        if (count($exist_records) > 0) {
            if (count($exist_records) == 1) {
                PitDetection::query()->where('id', $exist_records[0]->id)
                ->update([
                    'rule_id' => $rule_id,
                    'nb_entry' => $nb_entry,
                    'nb_exit' => $nb_exit,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
            }

            return response()->json(['success' => '送信成功'], 200);
        }

        $time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime, new \DateTimeZone('+0900'));
        $record_end_time_object = clone $time_object;
        if ((isset($request['detect_end_date']) && $request['detect_end_date'] != '')) {
            $end_datetime = date('Y-m-d H:i:s', strtotime($request['detect_end_date']));
            $record_end_time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $end_datetime, new \DateTimeZone('+0900'));
        } else {
            $record_end_time_object->add(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        }
        $record_end_time = $record_end_time_object->format('c');
        $record_start_time_object = clone $time_object;
        $record_start_time_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        $record_start_time = $record_start_time_object->format('c');
        Log::info('record startdatetime = '.$record_start_time);
        Log::info('record enddatetime = '.$record_end_time);

        $safie_service = new SafieApiService($camera_data->contract_no);

        $request_id = null;
        if ($camera_data->is_enabled == 1) {
            $request_id = $safie_service->makeMediaFile($camera_data->camera_id, $record_start_time, $record_end_time, 'ピット入退場検知', $camera_data->reopened_at);
        } else {
            Log::info('動画取得が中止されたカメラ '.$camera_data->camera_id);
        }

        Log::info('request_id = '.$request_id);
        if ($request_id > 0) {
            $temp_save_data = [
                'request_id' => $request_id,
                'starttime' => $start_datetime,
                'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                'device_id' => $camera_data->camera_id,
                'camera_id' => $camera_data->id,
                'contract_no' => $camera_data->contract_no,
                'rule_id' => $rule_id,
                'type' => 'pit',
                'nb_entry' => $nb_entry,
                'nb_exit' => $nb_exit,
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('ピット入退場解析結果送受信API（AI→BI）終了');

            return response()->json(['success' => '送信成功'], 200);
        } else {
            if ($request_id != null) {
                $http_code = str_replace('http_code_', '', $request_id);
                // if ($http_code == 503) {
                //     Log::info('ピット検知：メディアファイル 作成要求失敗ー503');
                //     Log::info('メディアファイル 作成要求臨時保存');
                //     $temp_save_data = [
                //         'record_start_time' => $record_start_time,
                //         'record_end_time' => $record_end_time,
                //         'starttime' => $start_datetime,
                //         'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                //         'device_id' => $camera_data->camera_id,
                //         'camera_id' => $camera_data->id,
                //         'contract_no' => $camera_data->contract_no,
                //         'rule_id' => $rule_id,
                //         'type' => 'pit',
                //         'nb_entry' => $nb_entry,
                //         'nb_exit' => $nb_exit,
                //         'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
                //     ];
                //     Storage::disk('temp')->put('media_request_503\\'.$camera_data->camera_id.'_pit_'.$record_start_time.'.json', json_encode($temp_save_data));
                // }
            }
            $detection_model = new PitDetection();
            $detection_model->nb_exit = $nb_exit;
            $detection_model->nb_entry = $nb_entry;
            $detection_model->camera_id = $camera_data->id;
            $detection_model->rule_id = $rule_id;
            $detection_model->video_file_path = '';
            $detection_model->starttime = $start_datetime;
            $detection_model->endtime = $record_end_time_object->format('Y-m-d H:i:s');
            $detection_model->thumb_img_path = '';
            $detection_model->save();

            Log::info('ピット入退場解析結果送受信API（AI→BI）終了');

            return response()->json(['error' => 'カメラメディアファイル作成失敗'], 200);
        }
    }

    public function saveThiefDetection(Request $request)
    {
        Log::info('大量盗難解析結果送受信API（AI→BI）開始');
        Log::info('パラメータ');
        Log::info($request);
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return response()->json(['error' => 'デバイスがありません。'], 400);
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return response()->json(['error' => '解析ルールがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return response()->json(['error' => '解析ルールIDがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return response()->json(['error' => '検知開始日時がありません。'], 400);
        }

        $rule_id = $request['analyze_result']['rect_id'];
        Log::info('rule id = '.$rule_id);
        $thief_service = new ThiefService();
        $camera_data = $thief_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        $detection_video_length = config('const.detection_video_length');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
        Log::info('start datetime = '.$start_datetime);

        $time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime, new \DateTimeZone('+0900'));
        $record_end_time_object = clone $time_object;
        if ((isset($request['detect_end_date']) && $request['detect_end_date'] != '')) {
            $end_datetime = date('Y-m-d H:i:s', strtotime($request['detect_end_date']));
            $record_end_time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $end_datetime, new \DateTimeZone('+0900'));
        } else {
            $record_end_time_object->add(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        }
        $record_end_time = $record_end_time_object->format('c');
        $record_start_time_object = clone $time_object;
        $record_start_time_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        $record_start_time = $record_start_time_object->format('c');
        Log::info('record startdatetime = '.$record_start_time);
        Log::info('record enddatetime = '.$record_end_time);

        $safie_service = new SafieApiService($camera_data->contract_no);

        $request_id = null;
        if ($camera_data->is_enabled == 1) {
            $request_id = $safie_service->makeMediaFile($camera_data->camera_id, $record_start_time, $record_end_time, '大量盗難検知', $camera_data->reopened_at);
        } else {
            Log::info('動画取得が中止されたカメラ '.$camera_data->camera_id);
        }

        Log::info('request_id = '.$request_id);
        if ($request_id > 0) {
            $temp_save_data = [
                'request_id' => $request_id,
                'starttime' => $start_datetime,
                'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                'device_id' => $camera_data->camera_id,
                'camera_id' => $camera_data->id,
                'contract_no' => $camera_data->contract_no,
                'rule_id' => $rule_id,
                'type' => 'thief',
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('大量盗難解析結果送受信API（AI→BI）終了');

            return response()->json(['success' => '送信成功'], 200);
        } else {
            if ($request_id != null) {
                $http_code = str_replace('http_code_', '', $request_id);
                // if ($http_code == 503) {
                //     Log::info('大量盗難：メディアファイル 作成要求失敗ー503');
                //     Log::info('メディアファイル 作成要求臨時保存');
                //     $temp_save_data = [
                //         'record_start_time' => $record_start_time,
                //         'record_end_time' => $record_end_time,
                //         'starttime' => $start_datetime,
                //         'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                //         'device_id' => $camera_data->camera_id,
                //         'camera_id' => $camera_data->id,
                //         'contract_no' => $camera_data->contract_no,
                //         'rule_id' => $rule_id,
                //         'type' => 'thief',
                //         'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
                //     ];
                //     Storage::disk('temp')->put('media_request_503\\'.$camera_data->camera_id.'_thief_'.$record_start_time.'.json', json_encode($temp_save_data));
                // }
            }
            Log::info('大量盗難解析結果送受信API（AI→BI）終了');

            return response()->json(['error' => 'カメラメディアファイル作成失敗'], 200);
        }
    }

    public function saveHeatmap(Request $request)
    {
        Log::info('ヒートマップ計算結果送受信API（AI→BI）開始');
        Log::info('パラメータ');
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return response()->json(['error' => 'デバイスがありません。'], 400);
        }
        if (!(isset($request['movie_info']) && isset($request['movie_info']['movie_path']) && $request['movie_info']['movie_path'] != '')) {
            Log::info('映像パスがありません。-------------');

            return response()->json(['error' => '映像パスがありません。'], 400);
        }
        if (!(isset($request['heatmap']) && $request['heatmap'] != '' && is_array($request['heatmap']))) {
            Log::info('ヒートマップデータがありません。-------------');

            return response()->json(['error' => 'ヒートマップデータがありません。'], 400);
        }
        $movie_path = $request['movie_info']['movie_path'];
        $movie_path = str_replace(config('const.ai_server'), '', $movie_path);
        Log::info('映像パス = '.$movie_path);
        $split_data = explode('_', $movie_path);
        if (count($split_data) <= 1) {
            Log::info('映像パスが正確ではありません。-------------');

            return response()->json(['error' => '映像パスが正確ではありません。'], 400);
        }
        $endtime = (int) str_replace('.mp4', '', $split_data[1]);
        Log::info('endtime = '.$endtime);
        $endtime = date('Y-m-d H:i:s', strtotime($endtime));
        Log::info('format endtime = '.$endtime);
        $split_data = explode('/', $split_data[0]);
        if (count($split_data) <= 1) {
            Log::info('映像パスが正確ではありません。-------------');

            return response()->json(['error' => '映像パスが正確ではありません。'], 400);
        }
        $starttime = $split_data[count($split_data) - 1];
        Log::info('starttime = '.$starttime);
        $starttime = date('Y-m-d H:i:s', strtotime($starttime));
        Log::info('format starttime = '.$starttime);
        $quality_score = 0.82;
        if (isset($request['quality_score']) && $request['quality_score'] > 0) {
            $quality_score = $request['quality_score'];
        }
        $camera_id = $request['camera_info']['camera_id'];
        Log::info('heatmap parameter*****************');
        // Log::info($request['heatmap']);
        $heatmap_data = $request['heatmap'];
        $check_flag = false;
        foreach ($heatmap_data as $rows) {
            foreach ($rows as &$item) {
                if (is_numeric($item)) {
                    $check_flag = true;
                } else {
                    $item = '';
                }
            }
        }
        $record = Heatmap::query()->where('camera_id', $camera_id)
            ->where('starttime', $starttime)
            ->where('endtime', $endtime)
            ->where('status', 1)->get()->first();
        if ($record != null) {
            DB::table('heatmaps')->where('id', $record->id)
                ->update([
                    'heatmap_data' => $check_flag ? json_encode($heatmap_data) : '',
                    'quality_score' => $quality_score,
                    'status' => 2,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
        if ($check_flag) {
            // $new_heatmap = new Heatmap();
            // $new_heatmap->camera_id = $camera_id;
            // $new_heatmap->quality_score = $quality_score;
            // $new_heatmap->heatmap_data = json_encode($heatmap_data);
            // $new_heatmap->starttime = $starttime;
            // $new_heatmap->endtime = $endtime;
            // $new_heatmap->time_diff = strtotime($endtime) - strtotime($starttime);
            // $new_heatmap->save();
            Log::info('ヒートマップ計算結果保存成功');
        } else {
            Log::info('すべてNaNデーターーーーーー');
        }

        Log::info('ヒートマップ計算結果送受信API（AI→BI）終了');

        return response()->json(['success' => '送信成功'], 200);
    }

    public function saveVehicleDetection(Request $request)
    {
        Log::info('車両の侵入検知解析結果送受信API（AI→BI）開始');
        Log::info('パラメータ');
        Log::info($request);
        if (!(isset($request['camera_info']) && isset($request['camera_info']['camera_id']) && $request['camera_info']['camera_id'] != '')) {
            Log::info('デバイスがありません。-------------');

            return response()->json(['error' => 'デバイスがありません。'], 400);
        }
        if (!isset($request['analyze_result'])) {
            Log::info('解析ルールがありません。-------------');

            return response()->json(['error' => '解析ルールがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['rect_id']) && $request['analyze_result']['rect_id'] > 0)) {
            Log::info('解析ルールIDがありません。-------------');

            return response()->json(['error' => '解析ルールIDがありません。'], 400);
        }
        if (!(isset($request['analyze_result']['detect_start_date']) && $request['analyze_result']['detect_start_date'] != '')) {
            Log::info('検知開始日時がありません。-------------');

            return response()->json(['error' => '検知開始日時がありません。'], 400);
        }
        if (!(isset($request['analyze_result']['nb_category']) && $request['analyze_result']['nb_category'] != '')) {
            Log::info('カテゴリデータがありません。-------------');

            return response()->json(['error' => 'カテゴリデータがありません。'], 400);
        }

        $rule_id = $request['analyze_result']['rect_id'];
        $nb_category = $request['analyze_result']['nb_category'];
        Log::info('rule id = '.$rule_id);
        Log::info('nb_category = '.$nb_category);

        $danger_service = new DangerService();
        $camera_data = $danger_service->getCameraByRuleID($rule_id);
        if ($camera_data == null) {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        if ($camera_data->contract_no == null || $camera_data->contract_no == '') {
            return response()->json(['error' => 'デバイスがありません。'], 500);
        }
        //send alert mails---------------
        $this->sendAlertMail($camera_data->serial_no, 'vc');
        //-------------------------------
        $detection_video_length = config('const.detection_video_length');
        $start_datetime = date('Y-m-d H:i:s', strtotime($request['analyze_result']['detect_start_date']));
        $exist_record = VcDetection::query()->where('camera_id', $camera_data->id)
            ->where('starttime', $start_datetime)
            ->get()->first();
        if ($exist_record != null) {
            VcDetection::query()->where('id', $exist_record->id)
                ->update([
                    'rule_id' => $rule_id,
                    'vc_category' => $nb_category,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            return response()->json(['success' => '送信成功'], 200);
        }

        Log::info('start datetime = '.$start_datetime);

        $time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime, new \DateTimeZone('+0900'));
        $record_end_time_object = clone $time_object;
        if ((isset($request['detect_end_date']) && $request['detect_end_date'] != '')) {
            $end_datetime = date('Y-m-d H:i:s', strtotime($request['detect_end_date']));
            $record_end_time_object = \DateTime::createFromFormat('Y-m-d H:i:s', $end_datetime, new \DateTimeZone('+0900'));
        } else {
            $record_end_time_object->add(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        }
        $record_end_time = $record_end_time_object->format('c');
        $record_start_time_object = clone $time_object;
        $record_start_time_object->sub(new \DateInterval('PT'.(string) $detection_video_length.'S'));
        $record_start_time = $record_start_time_object->format('c');
        Log::info('record startdatetime = '.$record_start_time);
        Log::info('record enddatetime = '.$record_end_time);

        $safie_service = new SafieApiService($camera_data->contract_no);
        $request_id = null;
        if ($camera_data->is_enabled == 1) {
            $request_id = $safie_service->makeMediaFile($camera_data->camera_id, $record_start_time, $record_end_time, '車両エリア侵入検知', $camera_data->reopened_at);
        } else {
            Log::info('動画取得が中止されたカメラ '.$camera_data->camera_id);
        }

        Log::info('request_id = '.$request_id);
        if ($request_id > 0) {
            $temp_save_data = [
                'request_id' => $request_id,
                'starttime' => $start_datetime,
                'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                'device_id' => $camera_data->camera_id,
                'camera_id' => $camera_data->id,
                'contract_no' => $camera_data->contract_no,
                'rule_id' => $rule_id,
                'vc_category' => $nb_category,
                'type' => 'vc',
                'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
            ];
            Storage::disk('temp')->put('video_request\\'.$request_id.'.json', json_encode($temp_save_data));
            Log::info('車両エリア侵入検知解析結果送受信API（AI→BI）終了');

            return response()->json(['success' => '送信成功'], 200);
        } else {
            if ($request_id != null) {
                $http_code = str_replace('http_code_', '', $request_id);
                // if ($http_code == 503) {
                //     Log::info('危険エリア侵入：メディアファイル 作成要求失敗ー503');
                //     Log::info('メディアファイル 作成要求臨時保存');
                //     $temp_save_data = [
                //         'record_start_time' => $record_start_time,
                //         'record_end_time' => $record_end_time,
                //         'starttime' => $start_datetime,
                //         'endtime' => $record_end_time_object->format('Y-m-d H:i:s'),
                //         'device_id' => $camera_data->camera_id,
                //         'camera_id' => $camera_data->id,
                //         'contract_no' => $camera_data->contract_no,
                //         'rule_id' => $rule_id,
                //         'detection_action_id' => $detection_action_id,
                //         'type' => 'danger_area',
                //         'starttime_format_for_image' => $time_object->format('Y-m-d\TH:i:sO'),
                //     ];
                //     Storage::disk('temp')->put('media_request_503\\'.$camera_data->camera_id.'_danger_area_'.$record_start_time.'.json', json_encode($temp_save_data));
                // }
            }
            $detection_model = new VcDetection();
            $detection_model->vc_category = $nb_category;
            $detection_model->camera_id = $camera_data->id;
            $detection_model->rule_id = $rule_id;
            $detection_model->video_file_path = '';
            $detection_model->starttime = $start_datetime;
            $detection_model->endtime = $record_end_time_object->format('Y-m-d H:i:s');
            $detection_model->thumb_img_path = '';
            $detection_model->save();
            Log::info('車両エリア侵入検知解析結果送受信API（AI→BI）終了');

            return response()->json(['error' => 'カメラメディアファイル作成失敗'], 200);
        }
    }

    public function sendAlertMail($camera_serail_no, $detect_type)
    {
        if ($camera_serail_no != 'B8A44F02E0B4') return true;
        $host = request()->getSchemeAndHttpHost();
        $url = $host.'/api/mail/sendInavasionMail?serial_no='.$camera_serail_no.'&detect_type='.$detect_type;
        $this->sendGetApi($url);
    }

    public function sendGetApi($url)
    {
        Log::info('【Start Get Api】url_for_alert_mail:'.$url);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode = '.$httpcode);
        curl_close($curl);

        if ($httpcode == 200) {
            return true;
        } else {
            return false;
        }
    }
}
