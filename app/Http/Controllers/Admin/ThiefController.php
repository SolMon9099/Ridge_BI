<?php

namespace App\Http\Controllers\Admin;

use App\Service\LocationService;
use App\Service\ThiefService;
use App\Models\Camera;
use App\Service\SafieApiService;
use App\Http\Requests\Admin\ThiefRequest;
use App\Models\ThiefDetectionRule;
use Illuminate\Http\Request;
use App\Models\CameraMappingDetail;
use Illuminate\Support\Facades\Auth;
use App\Service\CameraService;
use Illuminate\Support\Facades\Storage;

class ThiefController extends AdminController
{
    public function index(Request $request)
    {
        $thiefs = ThiefService::doSearch($request)->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();
        $cameras = ThiefService::getAllCameras();
        $camera_imgs = [];
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
            }
            $safie_service = new SafieApiService($camera->contract_no);

            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
                Storage::disk('recent_camera_image')->put($camera->camera_id.'.jpeg', $camera_image_data);
            }
        }
        foreach ($thiefs as $thief) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $thief->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $thief->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.thief.index')->with([
            'thiefs' => $thiefs,
            'input' => $request,
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function cameras_for_rule()
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

        return view('admin.thief.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function create_rule(ThiefRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $camera_data = CameraService::getCameraInfoById($request['selected_camera']);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $rules = ThiefService::getRulesByCameraID($request['selected_camera']);

        return view('admin.thief.create_rule')->with([
            'camera_id' => $request['selected_camera'],
            'rules' => $rules,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function edit(Request $request, ThiefDetectionRule $thief)
    {
        $camera_data = CameraService::getCameraInfoById($thief->camera_id);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $rules = ThiefService::getRulesByCameraID($thief->camera_id);

        return view('admin.thief.edit')->with([
            'thief' => $thief,
            'camera_id' => $thief->camera_id,
            'rules' => $rules,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function delete(Request $request, ThiefDetectionRule $thief)
    {
        if (ThiefService::doDelete($thief)) {
            $request->session()->flash('success', 'ルールを削除しました。');

            return redirect()->route('admin.thief');
        } else {
            $request->session()->flash('error', 'ルール削除が失敗しました。');

            return redirect()->route('admin.thief');
        }
    }

    public function store(ThiefRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $operation_type = '変更';
        if (isset($request['operation_type']) && $request['operation_type'] == 'register') {
            $operation_type = '追加';
        }
        if (ThiefService::saveData($request)) {
            $request->session()->flash('success', 'ルールを'.$operation_type.'しました。');

            return redirect()->route('admin.thief');
        } else {
            $request->session()->flash('error', 'ルール'.$operation_type.'に失敗しました。');

            return redirect()->route('admin.thief');
        }
    }

    public function list(Request $request)
    {
        $thief_detections = ThiefService::searchDetections($request)->paginate($this->per_page);
        foreach ($thief_detections as $item) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $item->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $item->floor_number = $map_data->floor_number;
            }
        }
        $rules = ThiefService::doSearch($request)->get()->all();
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.thief.list')->with([
            'thief_detections' => $thief_detections,
            'request' => $request,
            'rules' => $rules,
        ]);
    }

    public function detail()
    {
        return view('admin.thief.detail');
    }
}
