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
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
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
        }

        return view('admin.danger.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
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
        if (DangerService::saveData($request)) {
            $request->session()->flash('success', '?????????????????????????????????');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', '???????????????????????????????????????');

            return redirect()->route('admin.danger');
        }
    }

    public function delete(Request $request, DangerAreaDetectionRule $danger)
    {
        if (DangerService::doDelete($danger)) {
            $request->session()->flash('success', '?????????????????????????????????');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', '???????????????????????????????????????');

            return redirect()->route('admin.danger');
        }
    }

    public function list(Request $request)
    {
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
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.danger.list')->with([
            'danger_detections' => $danger_detections,
            'request' => $request,
            'rules' => $rules,
        ]);
    }

    public function detail(Request $request)
    {
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
            $all_data[date('Y-m-d', strtotime($item->starttime))][$item->action_id][] = $item;
        }
        $rules = DangerService::doSearch($request)->get()->all();
        $cameras = DangerService::getAllCameras();

        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
        }
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.danger.detail')->with([
            'all_data' => json_encode($all_data),
            'request' => $request,
            'rules' => $rules,
            'cameras' => $cameras,
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
