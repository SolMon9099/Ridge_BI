<?php

namespace App\Http\Controllers\Admin;

use App\Service\LocationService;
use App\Service\ShelfService;
use App\Models\Camera;
use App\Service\SafieApiService;
use App\Http\Requests\Admin\ShelfRequest;
use App\Models\ShelfDetectionRule;
use App\Models\ShelfDetection;
use Illuminate\Http\Request;
use App\Models\CameraMappingDetail;
use Illuminate\Support\Facades\Auth;
use App\Service\CameraService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

        $rules = ShelfService::getRulesByCameraID($shelf->camera_id);

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
        $operation_type = '変更';
        if (isset($request['operation_type']) && $request['operation_type'] == 'register') {
            $operation_type = '追加';
        }
        if (ShelfService::saveData($request)) {
            $request->session()->flash('success', 'ルールを'.$operation_type.'しました。');

            return redirect()->route('admin.shelf');
        } else {
            $request->session()->flash('error', 'ルール'.$operation_type.'に失敗しました。');

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

    public function delete(Request $request, ShelfDetectionRule $shelf)
    {
        if (ShelfService::doDelete($shelf)) {
            $request->session()->flash('success', 'ルールを削除しました。');

            return redirect()->route('admin.shelf');
        } else {
            $request->session()->flash('error', 'ルール削除が失敗しました。');

            return redirect()->route('admin.shelf');
        }
    }

    public function list(Request $request)
    {
        $shelf_detections = ShelfService::searchDetections($request)->paginate($this->per_page);
        foreach ($shelf_detections as $item) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $item->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $item->floor_number = $map_data->floor_number;
            }
        }
        $rules = ShelfService::doSearch($request)->get()->all();
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.shelf.list')->with([
            'shelf_detections' => $shelf_detections,
            'request' => $request,
            'rules' => $rules,
        ]);
    }

    public function detail()
    {
        return view('admin.shelf.detail');
    }

    public function save_sorted_imgage(Request $request, ShelfDetection $detect)
    {
        $camera_data = CameraService::getCameraInfoById($detect['camera_id']);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $file_name = date('YmdHis').'.jpeg';
            $date = date('Ymd');
            $device_id = $camera_data->camera_id;
            Storage::disk('s3')->put('shelf_sorted/'.$device_id.'/'.$date.'/'.$file_name, $camera_image_data);
            DB::table('shelf_detections')->where('camera_id', $detect['camera_id'])->update(['sorted_flag' => 1]);
            $request->session()->flash('success', '画像を保存しました。');
        } else {
            $request->session()->flash('error', '画像保存が失敗しました。');
        }

        return redirect()->route('admin.shelf.list');
    }
}
