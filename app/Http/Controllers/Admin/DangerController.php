<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\DangerRequest;
use Illuminate\Http\Request;
use App\Service\DangerService;
use App\Service\LocationService;
use App\Service\SafieApiService;
use App\Models\Camera;
use App\Models\DangerAreaDetectionRule;

// use Illuminate\Support\Facades\Redirect;

class DangerController extends AdminController
{
    public function index(Request $request)
    {
        $dangers = DangerService::doSearch($request)->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();

        return view('admin.danger.index')->with([
            'dangers' => $dangers,
            'locations' => $locations,
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

    public function create_rule(DangerRequest $request)
    {
        $safie_service = new SafieApiService();
        $camera_image_data = $safie_service->getDeviceImage();
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $danger_rules = DangerService::getRulesByCameraID($request['selected_camera']);

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
        $safie_service = new SafieApiService();
        $camera_image_data = $safie_service->getDeviceImage();
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $rules = DangerService::getDangerInfoById($danger->id);

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
        $rule_data = json_decode($request['rule_data']);
        $rule_data = (array) $rule_data;
        if (DangerService::saveData($rule_data)) {
            $request->session()->flash('success', 'ルールを変更しました。');

            return redirect()->route('admin.danger');
        } else {
            $request->session()->flash('error', 'ルール変更に失敗しました。');

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

    public function list()
    {
        return view('admin.danger.list');
    }

    public function detail()
    {
        return view('admin.danger.detail');
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
