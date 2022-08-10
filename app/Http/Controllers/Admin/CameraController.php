<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CameraRequest;
use App\Http\Requests\Admin\LocationDrawingRequest;
use Illuminate\Http\Request;
use App\Service\CameraService;
use App\Service\LocationService;
use App\Service\LocationDrawingService;
use App\Models\Camera;
use App\Models\CameraMappingDetail;
use App\Models\LocationDrawing;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\Auth;

class CameraController extends AdminController
{
    public function index(Request $request)
    {
        $cameras = CameraService::doSearch($request)->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();
        $user_contract_no = Auth::guard('admin')->user()->contract_no;
        if ($user_contract_no != null) {
            $safie_service = new SafieApiService($user_contract_no);
        }
        foreach ($cameras as $camera) {
            if (!($request->has('floor_number') && $request->floor_number != '')) {
                $map_data = CameraMappingDetail::select()
                    ->where('camera_id', $camera->id)
                    ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                    ->get()->first();
                if ($map_data != null) {
                    $camera->floor_number = $map_data->floor_number;
                }
            }
            if ($camera->contract_no == null) {
                continue;
            }
            if ($user_contract_no == null) {
                $safie_service = new SafieApiService($camera->contract_no);
            }
            $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
            if ($camera_image_data != null) {
                $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
            }
            $camera->img = $camera_image_data;
        }

        return view('admin.camera.index')->with([
            'cameras' => $cameras,
            'locations' => $locations,
            'input' => $request,
        ]);
    }

    public function create()
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $locations = LocationService::getAllLocationNames();
        $drawing_data = LocationDrawingService::getDrawingDataObject($locations);
        $safie_service = new SafieApiService(Auth::guard('admin')->user()->contract_no);
        $devices = $safie_service->getAllDevices();

        return view('admin.camera.create')->with([
            'locations' => $locations,
            'devices' => $devices,
            'drawing_data' => $drawing_data,
        ]);
    }

    public function edit(Request $request, Camera $camera)
    {
        $locations = LocationService::getAllLocationNames();
        $drawing_data = LocationDrawingService::getDrawingDataObject($locations);
        if ($camera->contract_no != null) {
            $safie_service = new SafieApiService($camera->contract_no);
            $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
            if ($camera_image_data != null) {
                $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
            }
            $camera->img = $camera_image_data;
            $map_data = CameraMappingDetail::select('drawing.*', 'camera_mapping_details.x_coordinate', 'camera_mapping_details.y_coordinate')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->drawing_id = $map_data->id;
                $camera->drawing_file_path = $map_data->drawing_file_path;
                $camera->floor_number = $map_data->floor_number;
                $camera->x_coordinate = $map_data->x_coordinate;
                $camera->y_coordinate = $map_data->y_coordinate;
            }
        }

        return view('admin.camera.edit')->with([
            'camera' => $camera,
            'locations' => $locations,
            'drawing_data' => $drawing_data,
        ]);
    }

    public function mapping(Request $request)
    {
        $locations = LocationService::getAllLocationNames();
        $drawings = LocationDrawingService::doSearch($request)->paginate($this->per_page);

        return view('admin.camera.mapping')->with([
            'locations' => $locations,
            'drawings' => $drawings,
            'input' => $request,
        ]);
    }

    public function store_mapping(Request $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $camera_mapping_info = json_decode($request['camera_mapping_info']);
        if (CameraService::storeMapping($camera_mapping_info)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.camera.mapping');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.camera.mapping');
        }
    }

    public function mappingDetail(Request $request, LocationDrawing $drawing)
    {
        $drawings = LocationDrawingService::getDataByLocation($drawing->location_id);
        $cameras = CameraService::getCameraByLocation($drawing->location_id);
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
            }
        }
        $camera_mapping_info = [];
        foreach ($drawings as $drawing_item) {
            $camera_mapping_info[$drawing_item->id] = $drawing_item->obj_camera_mappings;
        }

        return view('admin.camera.mapping_detail')->with([
            'drawings' => $drawings,
            'selected_drawing' => $drawing,
            'camera_mapping_info' => $camera_mapping_info,
            'cameras' => $cameras,
        ]);
    }

    public function edit_drawing(Request $request, LocationDrawing $drawing)
    {
        $locations = LocationService::getAllLocationNames();

        return view('admin.camera.edit_drawing')->with([
            'drawing' => $drawing,
            'locations' => $locations,
        ]);
    }

    public function create_drawing()
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $locations = LocationService::getAllLocationNames();

        return view('admin.camera.create_drawing')->with([
            'locations' => $locations,
        ]);
    }

    public function store(CameraRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        if (CameraService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.camera');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.camera');
        }
    }

    public function update(CameraRequest $request, Camera $camera)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        if (CameraService::doUpdate($request, $camera)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.camera');
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.camera');
        }
    }

    public function delete(Request $request, Camera $camera)
    {
        if (CameraService::doDelete($camera)) {
            $request->session()->flash('success', 'カメラを削除しました。');

            return redirect()->route('admin.camera');
        } else {
            $request->session()->flash('error', 'カメラ削除が失敗しました。');

            return redirect()->route('admin.camera');
        }
    }

    public function store_drawing(LocationDrawingRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        if (LocationDrawingService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.camera.mapping');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.camera.mapping');
        }
    }

    public function update_drawing(LocationDrawingRequest $request, LocationDrawing $drawing)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        if (LocationDrawingService::doUpdate($request, $drawing)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.camera.mapping');
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.camera.mapping');
        }
    }

    public function delete_drawing(Request $request, LocationDrawing $drawing)
    {
        if (LocationDrawingService::doDelete($drawing)) {
            $request->session()->flash('success', 'カメラを削除しました。');

            return redirect()->route('admin.camera.mapping');
        } else {
            $request->session()->flash('error', 'カメラ削除が失敗しました。');

            return redirect()->route('admin.camera.mapping');
        }
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
