<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Service\DangerService;
use App\Models\SearchOption;
use App\Service\TopService;
use Illuminate\Support\Facades\Auth;
use App\Models\CameraMappingDetail;
use App\Models\DangerAreaDetection;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\DB;

class VcController extends AdminController
{
    public function detail(Request $request)
    {
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $change_search_params_flag = false;
        if (isset($request['change_params']) && $request['change_params'] == 'change') {
            $change_search_params_flag = true;
        }
        $search_options = null;
        $search_option_record = SearchOption::query()->where('page_name', 'admin.vc.detail')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
        if ($search_option_record != null) {
            $search_options = $search_option_record->options;
        }

        if ($search_options != null && $change_search_params_flag == false) {
            $search_options = json_decode($search_options);
            $search_options = (array) $search_options;
            $request['selected_camera'] = $search_options['selected_camera'];
            $request['time_period'] = $search_options['time_period'];
            $rules = DangerService::doSearch($request)->orderByDesc('danger_area_detection_rules.id')->get()->all();
        } else {
            $rules = DangerService::doSearch($request)->orderByDesc('danger_area_detection_rules.id')->get()->all();
            if (!isset($request['selected_camera'])) {
                if (count($rules) > 0) {
                    $request['selected_camera'] = $rules[count($rules) - 1]->camera_id;
                }
            }
        }
        $search_params = [];
        if (isset($request['selected_camera']) && $request['selected_camera'] != '') {
            $search_params = [
                'selected_camera' => $request['selected_camera'],
                'time_period' => isset($request['time_period']) ? $request['time_period'] : 3,
            ];
            $search_option_params = [
                'page_name' => 'admin.vc.detail',
                'search_params' => $search_params,
            ];
            TopService::save_search_option($search_option_params);
        }

        $request['starttime'] = date('Y-m-d');
        $request['endtime'] = date('Y-m-d');
        $vc_detections = DangerService::searchVCDetections($request)->get()->all();
        $all_data = [];
        foreach ($vc_detections as $item) {
            if ($item->vc_category != '') {
                $all_data[date('H:i', strtotime($item->starttime))][$item->vc_category][] = $item;
            }
        }
        $access_token = '';
        $camera_imgs = [];
        $cameras = DangerService::getAllCameras();
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
            }
            if ($camera->contract_no == null) {
                continue;
            }
            $safie_service = new SafieApiService($camera->contract_no);
            if ($access_token == '') {
                if (isset($safie_service->access_token)) {
                    $access_token = $safie_service->access_token;
                }
            }
            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
            }
        }

        return view('admin.vc.detail')->with([
            'all_data' => json_encode(array_reverse($all_data)),
            'request_params' => (array) $search_params,
            'rules' => $rules,
            'cameras' => $cameras,
            'access_token' => $access_token,
            'vc_detections' => $vc_detections,
            'from_top' => $from_top,
            'last_number' => count($vc_detections) > 0 ? $vc_detections[0]->id : null,
        ]);
    }

    public function list(Request $request)
    {
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $vc_query = DangerService::searchVCDetections($request);
        $clone_vc_query = clone $vc_query;
        $vc_detections = $vc_query->paginate($this->per_page);
        $last_record = $clone_vc_query->get()->first();
        $start_times = [];
        foreach ($vc_detections as $item) {
            if (!isset($start_times[$item->rule_id])) {
                $start_times[$item->rule_id] = $item->starttime;
                $item->none_display_item = false;
            } else {
                if (strtotime($start_times[$item->rule_id]) - strtotime($item->starttime) > 1) {
                    $start_times[$item->rule_id] = $item->starttime;
                    $item->none_display_item = false;
                } else {
                    $item->none_display_item = true;
                }
            }
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $item->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $item->floor_number = $map_data->floor_number;
            }
            $check_starttime = date('Y-m-d H:i:s', strtotime($item->starttime) - 60);
            $check_endtime = date('Y-m-d H:i:s', strtotime($item->starttime) + 60);
            $dup_danger_detect_data = DangerAreaDetection::select('id', 'starttime')
                ->where('rule_id', $item->rule_id)
                ->where('starttime', '>=', $check_starttime)
                ->where('starttime', '<=', $check_endtime)
                ->get()->first();
            if ($dup_danger_detect_data != null) {
                $item->detect_duplicate = true;
            }
        }
        $rules = DangerService::getAllRules()->get()->all();
        $rule_cameras = [];
        $rule_actions = [];
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            } else {
                $rule->floor_number = '';
            }
            $last_detection = DB::table('vc_detections')->where('rule_id', $rule->id)->orderByDesc('starttime')->get()->first();
            $rule->img_path = $last_detection != null ? $last_detection->thumb_img_path : null;
            if (!in_array($rule->camera_id, array_keys($rule_cameras))) {
                $rule_cameras[$rule->camera_id] = [
                    'serial_no' => $rule->serial_no,
                    'device_id' => $rule->device_id,
                ];
            }
            foreach (json_decode($rule->action_id) as $action_code) {
                if (!in_array($action_code, $rule_actions)) {
                    $rule_actions[] = $action_code;
                }
            }
        }

        return view('admin.vc.list')->with([
            'vc_detections' => $vc_detections,
            'request' => $request,
            'rules' => $rules,
            'rule_cameras' => $rule_cameras,
            'rule_actions' => $rule_actions,
            'from_top' => $from_top,
            'last_number' => $last_record != null ? $last_record->id : null,
        ]);
    }

    public function past_analysis(Request $request)
    {
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $change_search_params_flag = false;
        if (isset($request['change_params']) && $request['change_params'] == 'change') {
            $change_search_params_flag = true;
        }

        $rules = DangerService::getAllRules()->get()->all();

        $search_options = null;
        $search_option_record = SearchOption::query()->where('page_name', 'admin.vc.past_analysis')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
        if ($search_option_record != null) {
            $search_options = $search_option_record->options;
        }

        if ($search_options != null && $change_search_params_flag == false) {
            $search_options = json_decode($search_options);
            $search_options = (array) $search_options;
            $request['starttime'] = $search_options['starttime'];
            $request['endtime'] = $search_options['endtime'];
            $request['time_period'] = $search_options['time_period'];
            // $request['selected_search_option'] = $search_options['selected_search_option'];
            $request['selected_rule'] = isset($search_options['selected_rule']) ? $search_options['selected_rule'] : (count($rules) > 0 ? $rules[0]->id : null);
            $request['selected_rules'] = isset($search_options['selected_rules']) ? $search_options['selected_rules'] : [];
            $request['selected_cameras'] = isset($search_options['selected_cameras']) ? $search_options['selected_cameras'] : [];
            $request['selected_actions'] = isset($search_options['selected_actions']) ? $search_options['selected_actions'] : [];
        }
        $search_params = [
            'starttime' => isset($request['starttime']) && $request['starttime'] != '' ? $request['starttime'] : date('Y-m-d', strtotime('-1 week')),
            'endtime' => isset($request['endtime']) && $request['endtime'] != '' ? $request['endtime'] : date('Y-m-d'),
            'time_period' => isset($request['time_period']) ? $request['time_period'] : 'time',
            // 'selected_search_option' => isset($request['selected_search_option']) ? $request['selected_search_option'] : 1,
            'selected_rule' => isset($request['selected_rule']) ? $request['selected_rule'] : null,
            'selected_rules' => isset($request['selected_rules']) ? $request['selected_rules'] : [],
            'selected_cameras' => isset($request['selected_cameras']) ? $request['selected_cameras'] : [],
            'selected_actions' => isset($request['selected_actions']) ? $request['selected_actions'] : [],
        ];
        $search_option_params = [
            'page_name' => 'admin.vc.past_analysis',
            'search_params' => $search_params,
        ];
        TopService::save_search_option($search_option_params);

        $vc_detections = DangerService::searchVCDetections($request)->get()->all();
        $all_data = [];
        foreach ($vc_detections as $item) {
            if ($item->vc_category != '') {
                $all_data[date('Y-m-d H:i', strtotime($item->starttime))][$item->vc_category][] = $item;
            }
        }

        $selected_rule_object = null;
        $rule_cameras = [];
        $rule_actions = [];
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                // ->whereNull('drawing.deleted_at')
                ->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            } else {
                $rule->floor_number = '';
            }
            if ($rule->id == $search_params['selected_rule']) {
                $selected_rule_object = $rule;
            }
            $last_detection = DB::table('vc_detections')->where('rule_id', $rule->id)->orderByDesc('starttime')->get()->first();
            $rule->img_path = $last_detection != null ? $last_detection->thumb_img_path : null;
            if (!in_array($rule->camera_id, array_keys($rule_cameras))) {
                $rule_cameras[$rule->camera_id] = [
                    'serial_no' => $rule->serial_no,
                    'device_id' => $rule->device_id,
                ];
            }
            foreach (json_decode($rule->action_id) as $action_code) {
                if (!in_array($action_code, $rule_actions)) {
                    $rule_actions[] = $action_code;
                }
            }
        }
        if ($search_params['selected_rule'] > 0 && $selected_rule_object == null) {
            $selected_rule_object = DangerService::doSearch(['selected_rule' => $search_params['selected_rule']])->get()->first();
        }

        return view('admin.vc.past_analysis')->with([
            'all_data' => json_encode(array_reverse($all_data)),
            'request_params' => (array) $search_params,
            'rules' => $rules,
            'rule_cameras' => $rule_cameras,
            'rule_actions' => $rule_actions,
            'selected_rule_object' => $selected_rule_object,
            'from_top' => $from_top,
            'last_number' => count($vc_detections) > 0 ? $vc_detections[0]->id : null,
        ]);
    }
}
