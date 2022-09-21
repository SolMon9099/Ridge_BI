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
use App\Service\CameraService;
// use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

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
        $rules = DangerService::doSearch($request)->orderByDesc('danger_area_detection_rules.id')->get()->all();
        $cameras = DangerService::getAllCameras();
        $request['starttime'] = date('Y-m-d');
        $request['endtime'] = date('Y-m-d');
        if (!isset($request['selected_cameras'])) {
            if (count($rules) > 0) {
                $request['selected_cameras'] = [$rules[count($rules) - 1]->camera_id];
            }
        }
        $danger_detections = DangerService::searchDetections($request)->get()->all();
        $all_data = [];
        foreach ($danger_detections as $item) {
            if ($item->detection_action_id > 0) {
                $all_data[date('H:i', strtotime($item->starttime))][$item->detection_action_id][] = $item;
            }
        }
        $access_token = '';
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
            if ($access_token == '') {
                if (isset($safie_service->access_token)) {
                    $access_token = $safie_service->access_token;
                }
            }
            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                if ($camera_image_data != null) {
                    $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
                }
                $camera_imgs[$camera->camera_id] = $camera_image_data;
            }

            $camera->img = $camera_imgs[$camera->camera_id];
        }

        return view('admin.danger.detail')->with([
            'all_data' => json_encode(array_reverse($all_data)),
            'request' => $request,
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
                if ($camera_image_data != null) {
                    $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
                }
                $camera_imgs[$camera->camera_id] = $camera_image_data;
            }
            $camera->img = $camera_imgs[$camera->camera_id];
        }
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
            if (isset($camera_imgs[$rule->camera_no])) {
                $rule->img = $camera_imgs[$rule->camera_no];
            }
        }

        return view('admin.danger.past_analysis')->with([
            'all_data' => json_encode(array_reverse($all_data)),
            'request' => $request,
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
