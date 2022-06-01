<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\DangerRequest;
use Illuminate\Http\Request;
use App\Service\DangerService;
use App\Service\LocationService;
use App\Models\Camera;
use App\Models\DangerAreaDetectionRule;

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
        $danger_rules = DangerService::getRulesByCameraID($request['selected_camera']);

        return view('admin.danger.create_rule')->with([
            'camera_id' => $request['selected_camera'],
            'rules' => $danger_rules,
        ]);
    }

    public function edit(Request $request, DangerAreaDetectionRule $danger)
    {
        $danger_rules = DangerService::getRulesByCameraID($danger->camera_id);

        return view('admin.danger.edit')->with([
            'danger' => $danger,
            'camera_id' => $danger->camera_id,
            'rules' => $danger_rules,
        ]);
    }

    public function store(DangerRequest $request)
    {
        $rule_data = json_decode($request['rule_data']);
        $rule_data = (array) $rule_data;
        if (DangerService::saveData($rule_data)) {
            if (isset($rule_data['id']) && $rule_data['id'] > 0) {
                $request->session()->flash('success', '変更しました。');

                return redirect()->route('admin.danger.edit', $rule_data['id']);
            } else {
                $request->session()->flash('success', '登録しました。');

                return redirect()->route('admin.danger.create_rule', ['selected_camera' => $rule_data['camera_id']]);
            }
        } else {
            if (isset($rule_data['id']) && $rule_data['id'] > 0) {
                $request->session()->flash('success', '変更に失敗しました。');

                return redirect()->route('admin.danger.edit', $rule_data['id']);
            } else {
                $request->session()->flash('success', '登録に失敗しました。', ['selected_camera' => $rule_data['camera_id']]);

                return redirect()->route('admin.danger.create_rule');
            }
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
