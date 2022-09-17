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

class ThiefController extends AdminController
{
    public function index()
    {
        $thiefs = ThiefService::doSearch()->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();
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
            'locations' => $locations,
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

    public function list()
    {
        return view('admin.thief.list');
    }

    public function detail()
    {
        return view('admin.thief.detail');
    }
}
