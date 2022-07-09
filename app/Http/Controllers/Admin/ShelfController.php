<?php

namespace App\Http\Controllers\Admin;

use App\Service\LocationService;
use App\Service\ShelfService;
use App\Models\Camera;
use App\Service\SafieApiService;
use App\Http\Requests\Admin\ShelfRequest;
use App\Models\ShelfDetectionRule;
use Illuminate\Http\Request;
use App\Models\CameraMappingDetail;
use Illuminate\Support\Facades\Auth;
use App\Service\CameraService;

class ShelfController extends AdminController
{
    public function index()
    {
        $shelfs = ShelfService::doSearch()->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();
        foreach ($shelfs as $shelf) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $shelf->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $shelf->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.shelf.index')->with([
            'shelfs' => $shelfs,
            'locations' => $locations,
        ]);
    }

    public function edit(Request $request, ShelfDetectionRule $shelf)
    {
        $camera_data = CameraService::getCameraInfoById($shelf->camera_id);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $rules = ShelfService::getShelfRuleInfoById($shelf->id);

        return view('admin.shelf.edit')->with([
            'shelf' => $shelf,
            'camera_id' => $shelf->camera_id,
            'rules' => $rules,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function store(ShelfRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $rule_data = json_decode($request['rule_data']);
        $rule_data = (array) $rule_data;
        if (ShelfService::saveData($rule_data)) {
            $request->session()->flash('success', 'ルールを変更しました。');

            return redirect()->route('admin.shelf');
        } else {
            $request->session()->flash('error', 'ルール変更に失敗しました。');

            return redirect()->route('admin.shelf');
        }
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

        return view('admin.shelf.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function create_rule(ShelfRequest $request)
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

        $rules = ShelfService::getRulesByCameraID($request['selected_camera']);

        return view('admin.shelf.create_rule')->with([
            'camera_id' => $request['selected_camera'],
            'rules' => $rules,
            'camera_image_data' => $camera_image_data,
            'device_id' => $safie_service->device_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function list()
    {
        return view('admin.shelf.list');
    }

    public function detail()
    {
        return view('admin.shelf.detail');
    }
}
