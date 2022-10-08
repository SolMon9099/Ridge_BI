<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PitRequest;
use Illuminate\Http\Request;
use App\Service\PitService;
use App\Service\LocationService;
use App\Service\SafieApiService;
use App\Models\Camera;
use App\Models\PitDetectionRule;
use App\Models\CameraMappingDetail;
use App\Models\SearchOption;
use App\Service\CameraService;
use App\Service\TopService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PitController extends AdminController
{
    public function index(Request $request)
    {
        $pits = PitService::doSearch($request)->paginate($this->per_page);
        $locations = LocationService::getAllLocationNames();
        $cameras = PitService::getAllCameras();
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

        foreach ($pits as $pit) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $pit->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $pit->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.pit.index')->with([
            'pits' => $pits,
            'locations' => $locations,
            'cameras' => $cameras,
            'input' => $request,
        ]);
    }

    public function cameras_for_rule(Request $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $pit_rules = PitService::doSearch()->get()->all();
        $temp = [];
        foreach ($pit_rules as $rule) {
            if (!isset($temp[$rule->camera_id])) {
                $temp[$rule->camera_id] = [];
            }
            $temp[$rule->camera_id][] = $rule;
        }
        $pit_rules = $temp;

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
            $camera->rules = isset($pit_rules[$camera->id]) ? $pit_rules[$camera->id] : [];
        }

        return view('admin.pit.cameras_for_rule')->with([
            'locations' => $locations,
            'cameras' => $cameras,
        ]);
    }

    public function create_rule(PitRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }

        $pit_rules = PitService::getRulesByCameraID($request['selected_camera']);
        $camera_data = CameraService::getCameraInfoById($request['selected_camera']);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        return view('admin.pit.create_rule')->with([
            'camera_id' => $request['selected_camera'],
            'rules' => $pit_rules,
            'camera_image_data' => $camera_image_data,
            'device_id' => $camera_data->camera_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function edit(Request $request, PitDetectionRule $pit)
    {
        $camera_data = CameraService::getCameraInfoById($pit->camera_id);
        $safie_service = new SafieApiService($camera_data->contract_no);
        $camera_image_data = $safie_service->getDeviceImage($camera_data->camera_id);
        if ($camera_image_data != null) {
            $camera_image_data = 'data:image/png;base64,'.base64_encode($camera_image_data);
        }

        $rules = PitService::getPitInfoById($pit->id);

        return view('admin.pit.edit')->with([
            'pit' => $pit,
            'rules' => $rules,
            'camera_id' => $pit->camera_id,
            'camera_image_data' => $camera_image_data,
            'device_id' => $camera_data->camera_id,
            'access_token' => $safie_service->access_token,
        ]);
    }

    public function store(PitRequest $request)
    {
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            abort(403);
        }
        $operation_type = '変更';
        if (isset($request['operation_type']) && $request['operation_type'] == 'register') {
            $operation_type = '追加';
        }
        if (PitService::saveData($request)) {
            $request->session()->flash('success', 'ルールを'.$operation_type.'しました。');

            return redirect()->route('admin.pit');
        } else {
            $request->session()->flash('error', 'ルール'.$operation_type.'に失敗しました。');

            return redirect()->route('admin.pit');
        }
    }

    public function delete(Request $request, PitDetectionRule $pit)
    {
        if (PitService::doDelete($pit)) {
            $request->session()->flash('success', 'ルールを削除しました。');

            return redirect()->route('admin.pit');
        } else {
            $request->session()->flash('error', 'ルール削除が失敗しました。');

            return redirect()->route('admin.pit');
        }
    }

    public function list(Request $request)
    {
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        if (!(isset($request['starttime']) && $request['starttime'] != '')) {
            $request['starttime'] = date('Y-m-d', strtotime('-1 week'));
        }
        // $pit_detections = PitService::searchDetections($request)->paginate($this->per_page);
        $pit_detections = PitService::searchDetections($request)->get()->all();
        $last_record = null;
        if (count($pit_detections) > 0) {
            $last_record = $pit_detections[0];
        }
        $pit_detections = array_reverse($pit_detections);
        $pit_detections = PitService::extractOverData($pit_detections);
        $pit_detections = array_reverse($pit_detections);
        $floor_data = [];
        foreach ($pit_detections as $item) {
            if (!isset($floor_data[$item->camera_id])) {
                $map_data = CameraMappingDetail::select('drawing.floor_number')
                    ->where('camera_id', $item->camera_id)
                    ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                    ->whereNull('drawing.deleted_at')->get()->first();
                if ($map_data != null) {
                    $floor_data[$item->camera_id] = $map_data->floor_number;
                } else {
                    $floor_data[$item->camera_id] = null;
                }
            }
            $item->floor_number = $floor_data[$item->camera_id];
        }
        $rules = PitService::doSearch($request)->get()->all();
        foreach ($rules as $rule) {
            $map_data = CameraMappingDetail::select('drawing.floor_number')
                ->where('camera_id', $rule->camera_id)
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                ->whereNull('drawing.deleted_at')->get()->first();
            if ($map_data != null) {
                $rule->floor_number = $map_data->floor_number;
            }
        }

        return view('admin.pit.list')->with([
            'pit_detections' => (array) $pit_detections,
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
        $search_option_record = SearchOption::query()->where('page_name', 'admin.pit.detail')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
        if ($search_option_record != null) {
            $search_options = $search_option_record->options;
        }
        if ($search_options != null && $change_search_params_flag == false) {
            $search_options = json_decode($search_options);
            $search_options = (array) $search_options;
            $request['selected_camera'] = $search_options['selected_camera'];
            $request['time_period'] = $search_options['time_period'];
            $selected_rule = PitService::doSearch($request)->orderByDesc('pit_detection_rules.id')->get()->first();
        } else {
            $selected_rule = PitService::doSearch($request)->orderByDesc('pit_detection_rules.id')->get()->first();
            if (!(isset($request['selected_camera']) && $request['selected_camera'] > 0)) {
                if ($selected_rule != null) {
                    $request['selected_camera'] = $selected_rule->camera_id;
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
                'page_name' => 'admin.pit.detail',
                'search_params' => $search_params,
            ];
            TopService::save_search_option($search_option_params);
        }

        if ($selected_rule != null) {
            if ($selected_rule->red_points != null && $selected_rule->red_points != '') {
                $selected_rule->red_points = json_decode($selected_rule->red_points);
            }
            if ($selected_rule->blue_points != null && $selected_rule->blue_points != '') {
                $selected_rule->blue_points = json_decode($selected_rule->blue_points);
            }
        }

        $pit_detections = PitService::searchDetections($request, true)->get()->all();
        $pit_over_detections = array_reverse($pit_detections);
        $pit_over_detections = PitService::extractOverData($pit_over_detections);
        $cameras = PitService::getAllCameras();
        $access_token = '';
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
            if ($access_token == '') {
                if (isset($safie_service->access_token)) {
                    $access_token = $safie_service->access_token;
                }
            }

            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
                Storage::disk('recent_camera_image')->put($camera->camera_id.'.jpeg', $camera_image_data);
            }
        }

        return view('admin.pit.detail')->with([
            'pit_detections' => $pit_detections,
            'pit_over_detections' => array_reverse($pit_over_detections),
            'request_params' => $search_params,
            'selected_rule' => $selected_rule,
            'cameras' => $cameras,
            'access_token' => $access_token,
            'from_top' => $from_top,
            'last_number' => count($pit_detections) > 0 ? $pit_detections[0]->id : null,
        ]);
    }

    public function past_analysis(Request $request)
    {
        $cameras = PitService::getAllCameras();
        $camera_imgs = [];
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

            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
                Storage::disk('recent_camera_image')->put($camera->camera_id.'.jpeg', $camera_image_data);
            }
        }
        $from_top = false;
        if (isset($request['from_top']) && $request['from_top'] == true) {
            $from_top = true;
        }
        $change_search_params_flag = false;
        if (isset($request['change_params']) && $request['change_params'] == 'change') {
            $change_search_params_flag = true;
        }

        $search_options = null;
        $search_option_record = SearchOption::query()->where('page_name', 'admin.pit.past_analysis')->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
        if ($search_option_record != null) {
            $search_options = $search_option_record->options;
        }

        if ($search_options != null && $change_search_params_flag == false) {
            $search_options = json_decode($search_options);
            $search_options = (array) $search_options;
            $request['selected_camera'] = $search_options['selected_camera'];
            $request['starttime'] = $search_options['starttime'];
            $request['endtime'] = $search_options['endtime'];
            $request['time_period'] = $search_options['time_period'];
            $selected_rule = PitService::doSearch($request)->orderByDesc('pit_detection_rules.id')->get()->first();
        } else {
            $selected_rule = PitService::doSearch($request)->orderByDesc('pit_detection_rules.id')->get()->first();
            if (!(isset($request['selected_camera']) && $request['selected_camera'] > 0)) {
                if ($selected_rule != null) {
                    $request['selected_camera'] = $selected_rule->camera_id;
                }
            }
        }
        $search_params = [];
        if (isset($request['selected_camera']) && $request['selected_camera'] != '') {
            $search_params = [
                'selected_camera' => $request['selected_camera'],
                'starttime' => isset($request['starttime']) && $request['starttime'] != '' ? $request['starttime'] : date('Y-m-d'),
                'endtime' => isset($request['endtime']) && $request['endtime'] != '' ? $request['endtime'] : date('Y-m-d'),
                'time_period' => isset($request['time_period']) ? $request['time_period'] : 3,
            ];
            $search_option_params = [
                'page_name' => 'admin.pit.past_analysis',
                'search_params' => $search_params,
            ];
            TopService::save_search_option($search_option_params);
        }

        $pit_detections = PitService::searchDetections($request)->get()->all();
        $pit_over_detections = array_reverse($pit_detections);
        $pit_over_detections = PitService::extractOverData($pit_over_detections);

        return view('admin.pit.past_analysis')->with([
            'pit_detections' => $pit_detections,
            'pit_over_detections' => array_reverse($pit_over_detections),
            'request_params' => (array) $search_params,
            'cameras' => $cameras,
            'selected_rule' => $selected_rule,
            'from_top' => $from_top,
            'last_number' => count($pit_detections) > 0 ? $pit_detections[0]->id : null,
        ]);
    }

    public function ajaxGetData(Request $request)
    {
        $pit_detections = PitService::searchDetections($request, true)->get()->all();

        return $pit_detections;
    }
}
