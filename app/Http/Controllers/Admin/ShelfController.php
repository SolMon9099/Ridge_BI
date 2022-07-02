<?php

namespace App\Http\Controllers\Admin;

use App\Service\LocationService;
use App\Service\ShelfService;
use App\Models\Camera;
use App\Service\SafieApiService;
use App\Http\Requests\Admin\ShelfRequest;
use App\Models\ShelfDetectionRule;
use Illuminate\Http\Request;

class ShelfController extends AdminController
{
    public function index()
    {
        $shelfs = ShelfService::doSearch()->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();

        return view('admin.shelf.index')->with([
            'shelfs' => $shelfs,
            'locations' => $locations,
        ]);
    }

    public function edit(Request $request, ShelfDetectionRule $shelf)
    {
        $safie_service = new SafieApiService();
        $camera_image_data = $safie_service->getDeviceImage();
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
        $locations = LocationService::getAllLocationNames();
        $cameras = Camera::orderBy('id', 'asc')->paginate($this->per_page);

        return view('admin.shelf.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function create_rule(ShelfRequest $request)
    {
        $safie_service = new SafieApiService();
        $camera_image_data = $safie_service->getDeviceImage();
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

    public function list2()
    {
        return view('admin.shelf.list2');
    }
}
