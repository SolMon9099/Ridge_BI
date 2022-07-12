<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PitRequest;
use Illuminate\Http\Request;
use App\Service\PitService;
use App\Service\LocationService;
use App\Service\SafieApiService;
use App\Models\Camera;
use App\Models\PitDetectionRule;
use App\Models\CameraMappingDetail;
use App\Service\CameraService;
// use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class PitController extends AdminController
{
    public function index(Request $request)
    {
        $pits = PitService::doSearch($request)->paginate($this->per_page);
        foreach ($pits as $pit) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $pit->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $pit->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.pit.index')->with([
            'pits' => $pits,
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

        return view('admin.pit.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function create_rule(PitRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }

        $pit_rules = PitService::getRulesByCameraID($request['selected_camera']);
        $camera_data = CameraService::getCameraInfoById($request['selected_camera']);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        return view('admin.pit.create_rule')->with([
            'camera_id' => $request['selected_camera'],
            'rules' => $pit_rules,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function edit(Request $request, PitDetectionRule $pit)
    {
        $camera_data = CameraService::getCameraInfoById($pit->camera_id);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $rules = PitService::getPitInfoById($pit->id);

        return view('admin.pit.edit')->with([
            'pit' => $pit,
            'rules' => $rules,
            'camera_id' => $pit->camera_id,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function store(PitRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        if (PitService::saveData($request)) {
            $request->session()->flash('success', 'ルールを変更しました。');

            return redirect()->route('admin.pit');
        } else {
            $request->session()->flash('error', 'ルール変更に失敗しました。');

            return redirect()->route('admin.pit');
        }
    }

    public function delete(Request $request, PitDetectionRule $pit)
    {
        if (PitService::doDelete($pit)) {
            $request->session()->flash('success', 'ルールを削除しました。');

            return redirect()->route('admin.pit');
        } else {
            $request->session()->flash('error', 'ルール削除が失敗しました。');

            return redirect()->route('admin.pit');
        }
    }

    public function list(Request $request)
    {
        $pit_detections = PitService::searchDetections($request)->paginate($this->per_page);
        foreach ($pit_detections as $item) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $item->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $item->floor_number = $map_data->floor_number;
            }
        }
        $rules = PitService::doSearch($request)->get()->all();
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.pit.list')->with([
            'pit_detections' => $pit_detections,
            'request' => $request,
            'rules' => $rules,
        ]);
    }

    public function detail(Request $request)
    {
        $pit_detections = PitService::searchDetections($request)->get()->all();
        $all_data = [];
        foreach ($pit_detections as $item) {
            $all_data[date('Y-m-d', strtotime($item->starttime))][] = $item;
        }
        $rules = PitService::doSearch($request)->get()->all();
        $cameras = PitService::getAllCameras();

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

        return view('admin.pit.detail')->with([
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
