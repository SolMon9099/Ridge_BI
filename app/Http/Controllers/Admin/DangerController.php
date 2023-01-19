<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\DangerRequest;
use Illuminate\Http\Request;
use App\Service\DangerService;
use App\Service\LocationService;
use App\Service\SafieApiService;
use App\Models\DangerAreaDetectionRule;
use App\Models\CameraMappingDetail;
use App\Models\SearchOption;
use App\Models\VcDetection;
use App\Service\CameraService;
use App\Service\TopService;
// use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DangerController extends AdminController
{
    public function index(Request $request)
    {
        $locations = LocationService::getAllLocationNames();
        $cameras = DangerService::getAllCameras();
        $camera_imgs = [];
        $floor_numbers = [];
        $installation_positions = [];
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
                if (!in_array($map_data->floor_number, $floor_numbers)) {
                    $floor_numbers[] = $map_data->floor_number;
                }
            }
            if ($camera->installation_position != null && $camera->installation_position != '' && !in_array($camera->installation_position, $installation_positions)) {
                $installation_positions[] = $camera->installation_position;
            }
            $safie_service = new SafieApiService($camera->contract_no);

            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
            }
        }
        $dangers = DangerService::doSearch($request)->paginate($this->per_page);
        $all_rules = DangerService::doSearch()->get()->all();
        $temp = [];
        foreach ($all_rules as $rule) {
            if (!isset($temp[$rule->camera_id])) {
                $temp[$rule->camera_id] = [];
            }
            $temp[$rule->camera_id][] = $rule;
        }
        $all_rules = $temp;
        foreach ($dangers as $danger) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $danger->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $danger->floor_number = $map_data->floor_number;
            }
            $danger->rules = isset($all_rules[$danger->camera_id]) ? $all_rules[$danger->camera_id] : [];
        }

        return view('admin.danger.index')->with([
            'dangers' => $dangers,
            'input' => $request,
            'locations' => $locations,
            'cameras' => $cameras,
            'installation_positions' => $installation_positions,
            'floor_numbers' => $floor_numbers,
        ]);
    }

    public function cameras_for_rule(Request $request)
    {
        $from_add_button = false;
        if (isset($request['add_button']) && $request['add_button']) {
            $from_add_button = $request['add_button'];
        }
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            return redirect()->route('admin.top');
        }
        $danger_rules = DangerService::doSearch()->get()->all();
        $temp = [];
        foreach ($danger_rules as $rule) {
            if (!isset($temp[$rule->camera_id])) {
                $temp[$rule->camera_id] = [];
            }
            $temp[$rule->camera_id][] = $rule;
        }
        $danger_rules = $temp;

        $locations = LocationService::getAllLocationNames();
        $cameras = CameraService::getCamerasForRules()->paginate($this->per_page);
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
            }
            $camera->rules = isset($danger_rules[$camera->id]) ? $danger_rules[$camera->id] : [];
        }

        return view('admin.danger.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
            'from_add_button' => $from_add_button,
        ]);
    }

    public function create_rule(DangerRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            return redirect()->route('admin.top');
        }

        $danger_rules = DangerService::getRulesByCameraID($request['selected_camera']);
        $camera_data = CameraService::getCameraInfoById($request['selected_camera']);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_path = null;
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            // $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
            $camera_image_path = 'recent_camera_image/'.$camera_data->camera_id.'.jpeg';
        }

        return view('admin.danger.create_rule')->with([
            'camera_id' => $request['selected_camera'],
            'rules' => $danger_rules,
            'camera_image_path' => $camera_image_path,
            'device_id' => $camera_data->camera_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function rule_view(Request $request)
    {
        $rule = DangerService::getDangerInfoById($request['id'], false)->first();
        $last_detection = DB::table('danger_area_detections')->where('rule_id', $request['id'])->orderByDesc('starttime')->get()->first();
        $rules = [$rule];
        $camera_data = CameraService::getCameraInfoById($rule->camera_id);
        $camera_image_path = null;
        if ($last_detection == null) {
            $safie_service = new SafieApiService($camera_data->contract_no);
            $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
            if ($camera_image_data != null) {
                // $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
                $camera_image_path = 'recent_camera_image/'.$camera_data->camera_id.'.jpeg';
            }
        } else {
            $camera_image_path = 'thumb/'.$last_detection->thumb_img_path;
        }

        return view('admin.danger.edit')->with([
            'danger' => $rule,
            'rules' => $rules,
            'camera_id' => $rule->camera_id,
            'camera_image_path' => $camera_image_path,
            'device_id' => $camera_data->camera_id,
            'view_only' => true,
        ]);
    }

    public function edit(Request $request, DangerAreaDetectionRule $danger)
    {
        $camera_data = CameraService::getCameraInfoById($danger->camera_id);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_path = null;
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            // $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
            $camera_image_path = 'recent_camera_image/'.$camera_data->camera_id.'.jpeg';
        }

        $rules = DangerService::getRulesByCameraID($danger->camera_id);

        return view('admin.danger.edit')->with([
            'danger' => $danger,
            'rules' => $rules,
            'camera_id' => $danger->camera_id,
            'camera_image_path' => $camera_image_path,
            'device_id' => $camera_data->camera_id,
        ]);
    }

    public function store(DangerRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            return redirect()->route('admin.top');
        }
        $operation_type = '変更';
        if (isset($request['operation_type']) && $request['operation_type'] == 'register') {
            $operation_type = '追加';
        }
        $register_res = DangerService::saveData($request);
        if ($register_res === true) {
            $request->session()->flash('success', 'ルールを'.$operation_type.'しました。');
        } else {
            if ($register_res === false) {
                $request->session()->flash('error', 'ルール'.$operation_type.'に失敗しました。');
            } else {
                $request->session()->flash('error', $register_res);
            }
        }

        return redirect()->route('admin.danger');
    }

    public function delete(Request $request, DangerAreaDetectionRule $danger)
    {
        if (DangerService::doDelete($danger)) {
            $request->session()->flash('success', 'ルールを削除しました。');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', 'ルール削除が失敗しました。');

            return redirect()->route('admin.danger');
        }
    }

    public function list(Request $request)
    {
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $danger_query = DangerService::searchDetections($request);
        $clone_danger_query = clone $danger_query;
        $danger_detections = $danger_query->paginate($this->per_page);
        $last_record = $clone_danger_query->get()->first();
        foreach ($danger_detections as $item) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $item->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $item->floor_number = $map_data->floor_number;
            }
            $check_starttime = date('Y-m-d H:i:s', strtotime($item->starttime) - 60);
            $check_endtime = date('Y-m-d H:i:s', strtotime($item->starttime) + 60);
            $dup_vc_detect_data = VcDetection::select('id', 'starttime')
                ->where('rule_id', $item->rule_id)
                ->where('starttime', '>=', $check_starttime)
                ->where('starttime', '<=', $check_endtime)
                ->get()->first();
            if ($dup_vc_detect_data != null) {
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
            $last_detection = DB::table('danger_area_detections')->where('rule_id', $rule->id)->orderByDesc('starttime')->get()->first();
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

        return view('admin.danger.list')->with([
            'danger_detections' => $danger_detections,
            'request' => $request,
            'rules' => $rules,
            'rule_cameras' => $rule_cameras,
            'rule_actions' => $rule_actions,
            'from_top' => $from_top,
            'last_number' => $last_record != null ? $last_record->id : null,
        ]);
    }

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
        $search_option_record = SearchOption::query()->where('page_name', 'admin.danger.detail')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
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
                'page_name' => 'admin.danger.detail',
                'search_params' => $search_params,
            ];
            TopService::save_search_option($search_option_params);
        }

        $request['starttime'] = date('Y-m-d');
        $request['endtime'] = date('Y-m-d');
        $danger_detections = DangerService::searchDetections($request)->get()->all();
        $all_data = [];
        foreach ($danger_detections as $item) {
            if ($item->detection_action_id > 0) {
                $all_data[date('H:i', strtotime($item->starttime))][$item->detection_action_id][] = $item;
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

        return view('admin.danger.detail')->with([
            'all_data' => json_encode(array_reverse($all_data)),
            'request_params' => (array) $search_params,
            'rules' => $rules,
            'cameras' => $cameras,
            'access_token' => $access_token,
            'danger_detections' => $danger_detections,
            'from_top' => $from_top,
            'last_number' => count($danger_detections) > 0 ? $danger_detections[0]->id : null,
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
        $search_option_record = SearchOption::query()->where('page_name', 'admin.danger.past_analysis')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
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
            'page_name' => 'admin.danger.past_analysis',
            'search_params' => $search_params,
        ];
        TopService::save_search_option($search_option_params);

        $danger_detections = DangerService::searchDetections($request)->get()->all();
        $all_data = [];
        foreach ($danger_detections as $item) {
            if ($item->detection_action_id > 0) {
                $all_data[date('Y-m-d H:i', strtotime($item->starttime))][$item->detection_action_id][] = $item;
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
            $last_detection = DB::table('danger_area_detections')->where('rule_id', $rule->id)->orderByDesc('starttime')->get()->first();
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

        return view('admin.danger.past_analysis')->with([
            'all_data' => json_encode(array_reverse($all_data)),
            'request_params' => (array) $search_params,
            'rules' => $rules,
            'rule_cameras' => $rule_cameras,
            'rule_actions' => $rule_actions,
            'selected_rule_object' => $selected_rule_object,
            'from_top' => $from_top,
            'last_number' => count($danger_detections) > 0 ? $danger_detections[0]->id : null,
        ]);
    }

    public function ajaxUploadFile(Request $request)
    {
        $download_file = $request->file('vfile');
        request()->validate([
            'vfile' => 'mimes:jpg,jpeg,png,bmp,tiff|max:10240',
        ]);
        $new_filename = 'drawing_'.date('YmdHis').'.'.$download_file->getClientOriginalExtension();
        $path = $request->file('vfile')->storeAs('public/temp', $new_filename);
        exit($new_filename);
    }
}
