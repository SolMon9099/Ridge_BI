<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\DangerRequest;
use Illuminate\Http\Request;
use App\Service\DangerService;
use App\Service\LocationService;
use App\Service\SafieApiService;
use App\Models\Camera;
use App\Models\DangerAreaDetectionRule;
use App\Models\CameraMappingDetail;
use App\Models\SearchOption;
use App\Service\CameraService;
use App\Service\TopService;
// use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DangerController extends AdminController
{
    public function index(Request $request)
    {
        $dangers = DangerService::doSearch($request)->paginate($this->per_page);
        foreach ($dangers as $daner) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $daner->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $daner->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.danger.index')->with([
            'dangers' => $dangers,
            'input' => $request,
        ]);
    }

    public function cameras_for_rule(Request $request)
    {
        $from_add_button = false;
        if (isset($request['add_button']) && $request['add_button']) {
            $from_add_button = $request['add_button'];
        }
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
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
        $camera_query = Camera::query();
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $cameras = $camera_query->orderBy('id', 'asc')->paginate($this->per_page);
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
            abort(403);
        }

        $danger_rules = DangerService::getRulesByCameraID($request['selected_camera']);
        $camera_data = CameraService::getCameraInfoById($request['selected_camera']);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        return view('admin.danger.create_rule')->with([
            'camera_id' => $request['selected_camera'],
            'rules' => $danger_rules,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function edit(Request $request, DangerAreaDetectionRule $danger)
    {
        $camera_data = CameraService::getCameraInfoById($danger->camera_id);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $rules = DangerService::getRulesByCameraID($danger->camera_id);

        return view('admin.danger.edit')->with([
            'danger' => $danger,
            'rules' => $rules,
            'camera_id' => $danger->camera_id,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function store(DangerRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $operation_type = '変更';
        if (isset($request['operation_type']) && $request['operation_type'] == 'register') {
            $operation_type = '追加';
        }
        if (DangerService::saveData($request)) {
            $request->session()->flash('success', 'ルールを'.$operation_type.'しました。');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', 'ルール'.$operation_type.'に失敗しました。');

            return redirect()->route('admin.danger');
        }
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
        $danger_detections = DangerService::searchDetections($request)->paginate($this->per_page);
        foreach ($danger_detections as $item) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $item->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $item->floor_number = $map_data->floor_number;
            }
        }
        $rules = DangerService::doSearch($request)->get()->all();
        $camera_imgs = [];
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
            if (!isset($camera_imgs[$rule->camera_no])) {
                if (isset($rule->contract_no)) {
                    $safie_service = new SafieApiService($rule->contract_no);
                    $camera_image_data = $safie_service->getDeviceImage($rule->camera_no);
                    if ($camera_image_data != null) {
                        $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
                    }
                    $camera_imgs[$rule->camera_no] = $camera_image_data;
                }
            }
            $rule->img = $camera_imgs[$rule->camera_no];
        }

        return view('admin.danger.list')->with([
            'danger_detections' => $danger_detections,
            'request' => $request,
            'rules' => $rules,
            'from_top' => $from_top,
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
                Storage::disk('recent_camera_image')->put($camera->camera_id.'.jpeg', $camera_image_data);
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
            $request['selected_search_option'] = $search_options['selected_search_option'];
            $request['selected_rules'] = isset($search_options['selected_rules']) ? $search_options['selected_rules'] : [];
            $request['selected_cameras'] = isset($search_options['selected_cameras']) ? $search_options['selected_cameras'] : [];
            $request['selected_actions'] = isset($search_options['selected_actions']) ? $search_options['selected_actions'] : [];
        }
        switch ($request['selected_search_option']) {
            case 1:
                $request['selected_cameras'] = [];
                $request['selected_actions'] = [];
                break;
            case 2:
                $request['selected_rules'] = [];
                $request['selected_actions'] = [];
                break;
            case 3:
                $request['selected_rules'] = [];
                $request['selected_cameras'] = [];
                break;
        }
        $search_params = [
            'starttime' => isset($request['starttime']) && $request['starttime'] != '' ? $request['starttime'] : date('Y-m-d', strtotime('-1 week')),
            'endtime' => isset($request['endtime']) && $request['endtime'] != '' ? $request['endtime'] : date('Y-m-d'),
            'time_period' => isset($request['time_period']) ? $request['time_period'] : 'time',
            'selected_search_option' => isset($request['selected_search_option']) ? $request['selected_search_option'] : 1,
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
        $rules = DangerService::doSearch($request)->get()->all();
        $cameras = DangerService::getAllCameras();
        $camera_imgs = [];
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

            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
                Storage::disk('recent_camera_image')->put($camera->camera_id.'.jpeg', $camera_image_data);
            }
        }
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.danger.past_analysis')->with([
            'all_data' => json_encode(array_reverse($all_data)),
            'request_params' => (array) $search_params,
            'rules' => $rules,
            'cameras' => $cameras,
            'from_top' => $from_top,
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
