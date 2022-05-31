<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CameraRequest;
use App\Http\Requests\Admin\LocationDrawingRequest;
use App\Service\CameraService;
use Illuminate\Http\Request;
use App\Service\DangerService;
use App\Service\LocationService;
use App\Service\LocationDrawingService;
use App\Models\Camera;
use App\Models\LocationDrawing;

class DangerController extends AdminController
{
    public function index(Request $request)
    {
        $dangers = DangerService::doSearch($request)->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();
        $cameras = CameraService::getAllCameraNames();
//        dd($cameras);

        return view('admin.danger.index')->with([
            'dangers' => $dangers,
            'locations' => $locations,
            'cameras' => $cameras,
            'input' => $request,
        ]);
    }

    public function cameras_for_rule(Request $request)
    {
        $locations = LocationService::getAllLocationNames();
        $cameras = Camera::orderBy('id', 'asc')->paginate($this->per_page);

        return view('admin.danger.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function create_rule(Request $request)
    {
        $locations = LocationService::getAllLocationNames();
        $cameras = Camera::orderBy('id', 'asc')->paginate($this->per_page);

        return view('admin.danger.create_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function edit(Request $request, Camera $danger)
    {
        $locations = LocationService::getAllLocationNames();

        return view('admin.danger.edit')->with([
            'danger' => $danger,
            'locations' => $locations,
        ]);
    }

    public function mapping(Request $request)
    {
        $locations = LocationService::getAllLocationNames();
        $drawings = LocationDrawingService::doSearch($request)->paginate($this->per_page);

        return view('admin.danger.mapping')->with([
            'locations' => $locations,
            'drawings' => $drawings,
            'input' => $request,
        ]);
    }

    public function store_mapping(Request $request)
    {
        return view('admin.danger.mapping');
    }

    public function mappingDetail(Request $request, LocationDrawing $drawing)
    {
        $drawings = LocationDrawingService::getDataByLocation($drawing->location_id);
        $danger_mapping_info = [];
        foreach ($drawings as $drawing_item) {
            $danger_mapping_info[$drawing_item->id][] = $drawing_item->obj_danger_mappings();
        }

        return view('admin.danger.mapping_detail')->with([
            'drawings' => $drawings,
            'selected_drawing' => $drawing,
            'danger_mapping_info' => $danger_mapping_info,
        ]);
    }

    public function edit_drawing(Request $request, LocationDrawing $drawing)
    {
        $locations = LocationService::getAllLocationNames();

        return view('admin.danger.edit_drawing')->with([
            'drawing' => $drawing,
            'locations' => $locations,
        ]);
    }

    public function create_drawing()
    {
        $locations = LocationService::getAllLocationNames();

        return view('admin.danger.create_drawing')->with([
            'locations' => $locations,
        ]);
    }

    public function store(CameraRequest $request)
    {
        if (DangerService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.danger');
        }
    }

    public function update(CameraRequest $request, Camera $danger)
    {
        if (DangerService::doUpdate($request, $danger)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.danger');
        }
    }

    public function delete(Request $request, Camera $danger)
    {
        if (DangerService::doDelete($danger)) {
            $request->session()->flash('success', 'カメラを削除しました。');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', 'カメラ削除が失敗しました。');

            return redirect()->route('admin.danger');
        }
    }

    public function store_drawing(LocationDrawingRequest $request)
    {
        if (LocationDrawingService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.danger.mapping');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.danger.mapping');
        }
    }

    public function update_drawing(LocationDrawingRequest $request, LocationDrawing $drawing)
    {
        if (LocationDrawingService::doUpdate($request, $drawing)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.danger.mapping');
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.danger.mapping');
        }
    }

    public function delete_drawing(Request $request, LocationDrawing $drawing)
    {
        if (LocationDrawingService::doDelete($drawing)) {
            $request->session()->flash('success', 'カメラを削除しました。');

            return redirect()->route('admin.danger.mapping');
        } else {
            $request->session()->flash('error', 'カメラ削除が失敗しました。');

            return redirect()->route('admin.danger.mapping');
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
