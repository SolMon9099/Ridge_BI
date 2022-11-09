<?php

namespace App\Http\Controllers\Admin;

use App\Service\LocationService;
use App\Service\ShelfService;
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
use App\Models\SearchOption;
use App\Service\TopService;

class ShelfController extends AdminController
{
    public function index(Request $request)
    {
        $shelfs = ShelfService::doSearch($request)->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();
        $cameras = ShelfService::getAllCameras();
        $camera_imgs = [];
        $floor_numbers = [];
        $installation_positions = [];
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
                if (!in_array($map_data->floor_number, $floor_numbers)) {
                    $floor_numbers[] = $map_data->floor_number;
                }
            }
            if ($camera->installation_position != null && $camera->installation_position != '' && !in_array($camera->installation_position, $installation_positions)) {
                $installation_positions[] = $camera->installation_position;
            }
            $safie_service = new SafieApiService($camera->contract_no);

            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
            }
        }
        $all_rules = ShelfService::doSearch()->get()->all();
        $temp = [];
        foreach ($all_rules as $rule) {
            if (!isset($temp[$rule->camera_id])) {
                $temp[$rule->camera_id] = [];
            }
            $temp[$rule->camera_id][] = $rule;
        }
        $all_rules = $temp;

        foreach ($shelfs as $shelf) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $shelf->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $shelf->floor_number = $map_data->floor_number;
            }
            $shelf->rules = isset($all_rules[$shelf->camera_id]) ? $all_rules[$shelf->camera_id] : [];
        }

        return view('admin.shelf.index')->with([
            'shelfs' => $shelfs,
            'input' => $request,
            'locations' => $locations,
            'cameras' => $cameras,
            'installation_positions' => $installation_positions,
            'floor_numbers' => $floor_numbers,
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
        $register_res = ShelfService::saveData($request);
        if ($register_res === true) {
            $request->session()->flash('success', 'ルールを'.$operation_type.'しました。');
        } else {
            if ($register_res === false) {
                $request->session()->flash('error', 'ルール'.$operation_type.'に失敗しました。');
            } else {
                $request->session()->flash('error', $register_res);
            }
        }

        return redirect()->route('admin.shelf');
    }

    public function cameras_for_rule(Request $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $from_add_button = false;
        if (isset($request['add_button']) && $request['add_button']) {
            $from_add_button = $request['add_button'];
        }
        $rules = ShelfService::doSearch()->get()->all();
        $temp = [];
        foreach ($rules as $rule) {
            if (!isset($temp[$rule->camera_id])) {
                $temp[$rule->camera_id] = [];
            }
            $temp[$rule->camera_id][] = $rule;
        }
        $rules = $temp;

        $locations = LocationService::getAllLocationNames();
        $cameras = CameraService::getCamerasForRules()->paginate($this->per_page);
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
            }
            $camera->rules = isset($rules[$camera->id]) ? $rules[$camera->id] : [];
        }

        return view('admin.shelf.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
            'from_add_button' => $from_add_button,
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
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $shelf_query = ShelfService::searchDetections($request);
        $clone_query = clone $shelf_query;
        $last_record = $clone_query->get()->first();
        $shelf_detections = $shelf_query->paginate($this->per_page);
        foreach ($shelf_detections as $item) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $item->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $item->floor_number = $map_data->floor_number;
            } else {
                $item->floor_number = '';
            }
        }
        $rules = ShelfService::getAllRules()->get()->all();
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            } else {
                $rule->floor_number = '';
            }
        }

        return view('admin.shelf.list')->with([
            'shelf_detections' => $shelf_detections,
            'request' => $request,
            'rules' => $rules,
            'from_top' => $from_top,
            'last_number' => $last_record != null ? $last_record->id : null,
        ]);
    }

    public function detail(Request $request)
    {
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $change_search_params_flag = false;
        if (isset($request['change_params']) && $request['change_params'] == 'change') {
            $change_search_params_flag = true;
        }
        $search_options = null;
        $search_option_record = SearchOption::query()->where('page_name', 'admin.shelf.detail')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
        if ($search_option_record != null) {
            $search_options = $search_option_record->options;
        }

        if ($search_options != null && $change_search_params_flag == false) {
            $search_options = json_decode($search_options);
            $search_options = (array) $search_options;
            $request['selected_camera'] = $search_options['selected_camera'];
            $request['time_period'] = $search_options['time_period'];
            $rules = ShelfService::doSearch($request)->orderByDesc('shelf_detection_rules.id')->get()->all();
        } else {
            $rules = ShelfService::doSearch($request)->orderByDesc('shelf_detection_rules.id')->get()->all();
            if (!isset($request['selected_camera'])) {
                if (count($rules) > 0) {
                    $request['selected_camera'] = $rules[count($rules) - 1]->camera_id;
                }
            }
        }
        $search_params = [];
        if (isset($request['selected_camera']) && $request['selected_camera'] != '') {
            $search_params = [
                'selected_camera' => $request['selected_camera'],
                'time_period' => isset($request['time_period']) ? $request['time_period'] : 3,
            ];
            $search_option_params = [
                'page_name' => 'admin.shelf.detail',
                'search_params' => $search_params,
            ];
            TopService::save_search_option($search_option_params);
        }

        $request['starttime'] = date('Y-m-d');
        $request['endtime'] = date('Y-m-d');
        $shelf_detections = ShelfService::searchDetections($request)->get()->all();
        $all_data = [];
        foreach (array_reverse($shelf_detections) as $item) {
            if (!isset($all_data[date('H:i', strtotime($item->starttime))])) {
                $all_data[date('H:i', strtotime($item->starttime))] = 0;
            }
            ++$all_data[date('H:i', strtotime($item->starttime))];
        }
        $access_token = '';
        $camera_imgs = [];
        $cameras = ShelfService::getAllCameras();
        foreach ($cameras as $camera) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $camera->id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $camera->floor_number = $map_data->floor_number;
            }
            if ($camera->contract_no == null) {
                continue;
            }
            $safie_service = new SafieApiService($camera->contract_no);
            if ($access_token == '') {
                if (isset($safie_service->access_token)) {
                    $access_token = $safie_service->access_token;
                }
            }
            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
            }
        }

        return view('admin.shelf.detail')->with([
            'all_data' => json_encode($all_data),
            'request_params' => (array) $search_params,
            'rules' => $rules,
            'cameras' => $cameras,
            'access_token' => $access_token,
            'shelf_detections' => $shelf_detections,
            'from_top' => $from_top,
            'last_number' => count($shelf_detections) > 0 ? $shelf_detections[0]->id : null,
        ]);
    }

    public function past_analysis(Request $request)
    {
        $rules = ShelfService::getAllRules()->get()->all();
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $change_search_params_flag = false;
        if (isset($request['change_params']) && $request['change_params'] == 'change') {
            $change_search_params_flag = true;
        }

        $search_options = null;
        $search_option_record = SearchOption::query()->where('page_name', 'admin.shelf.past_analysis')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
        if ($search_option_record != null) {
            $search_options = $search_option_record->options;
        }

        if ($search_options != null && $change_search_params_flag == false) {
            $search_options = json_decode($search_options);
            $search_options = (array) $search_options;
            // $request['selected_camera'] = $search_options['selected_camera'];
            $request['selected_rule'] = isset($search_options['selected_rule']) ? $search_options['selected_rule'] : (count($rules) > 0 ? $rules[0]->id : null);
            $request['starttime'] = $search_options['starttime'];
            $request['endtime'] = $search_options['endtime'];
            $request['time_period'] = $search_options['time_period'];
        } else {
            if (!(isset($request['selected_rule']) && $request['selected_rule'] > 0)) {
                if (count($rules) > 0) {
                    $request['selected_rule'] = $rules[0]->id;
                }
            }
        }
        $selected_rule_object = null;
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            } else {
                $rule->floor_number = '';
            }
            if ($request['selected_rule'] == $rule->id) {
                $selected_rule_object = $rule;
            }
        }
        $search_params = [];
        if ($selected_rule_object != null) {
            $search_params = [
                'selected_rule' => $request['selected_rule'],
                'starttime' => isset($request['starttime']) && $request['starttime'] != '' ? $request['starttime'] : date('Y-m-d', strtotime('-1 week')),
                'endtime' => isset($request['endtime']) && $request['endtime'] != '' ? $request['endtime'] : date('Y-m-d'),
                'time_period' => isset($request['time_period']) ? $request['time_period'] : 3,
            ];
            $search_option_params = [
                'page_name' => 'admin.shelf.past_analysis',
                'search_params' => $search_params,
            ];
            TopService::save_search_option($search_option_params);
        }

        $shelf_detections = ShelfService::searchDetections($request)->get()->all();

        return view('admin.shelf.past_analysis')->with([
            'shelf_detections' => array_reverse($shelf_detections),
            'request_params' => (array) $search_params,
            'rules' => $rules,
            'selected_rule_object' => $selected_rule_object,
            'from_top' => $from_top,
            'last_number' => count($shelf_detections) > 0 ? $shelf_detections[0]->id : null,
        ]);
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
