<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AuthorityGroup;
use App\Models\TopBlock;
use Illuminate\Support\Facades\Auth;
use App\Service\DangerService;
use App\Service\PitService;
use App\Service\SafieApiService;
use App\Service\TopService;

class TopController extends AdminController
{
    public function index(Request $request_params)
    {
        $top_blocks = TopService::search()->get()->all();
        foreach ($top_blocks as $item) {
            $request = [];
            switch ($item->block_type) {
                case config('const.top_block_type_codes')['live_video_danger']:
                    if (!isset($danger_cameras)) {
                        $danger_cameras = DangerService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($danger_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            $temp[] = $camera;
                        }
                        $danger_cameras = $temp;
                    }
                    $item->cameras = $danger_cameras;

                    $item->selected_camera = null;
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['selected_camera']) && $options['selected_camera'] > 0) {
                            $request['selected_camera'] = $options['selected_camera'];
                            foreach ($item->cameras as $camera_item) {
                                if ($camera_item['id'] == $options['selected_camera']) {
                                    $item->selected_camera = $camera_item;
                                }
                            }
                        }
                    } else {
                        $item->selected_camera = count($item->cameras) > 0 ? $item->cameras[0] : null;
                    }
                    $item->rules = null;
                    if ($item->selected_camera != null) {
                        $item->rules = DangerService::getRulesByCameraID($item->selected_camera['id'])->toArray();
                    }
                    break;
                case config('const.top_block_type_codes')['recent_detect_danger']:
                    if (!isset($danger_cameras)) {
                        $danger_cameras = DangerService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($danger_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            $temp[] = $camera;
                        }
                        $danger_cameras = $temp;
                    }
                    $item->cameras = $danger_cameras;

                    $item->selected_camera = null;
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['selected_camera']) && $options['selected_camera'] > 0) {
                            $request['selected_camera'] = $options['selected_camera'];
                            foreach ($item->cameras as $camera_item) {
                                if ($camera_item['id'] == $options['selected_camera']) {
                                    $item->selected_camera = $camera_item;
                                }
                            }
                        }
                    } else {
                        $item->selected_camera = count($item->cameras) > 0 ? $item->cameras[0] : null;
                    }

                    $request['starttime'] = date('Y-m-d');
                    $request['endtime'] = date('Y-m-d');
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['starttime']) && $options['starttime'] != '') {
                            $request['starttime'] = $options['starttime'];
                        }
                        if (isset($options['endtime']) && $options['endtime'] != '') {
                            $request['endtime'] = $options['endtime'];
                        }
                    }
                    $unlimit_danger_detections = DangerService::searchDetections($request)->get()->toArray();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->danger_detections = $unlimit_danger_detections;
                    $item->danger_detection = count($unlimit_danger_detections) > 0 ? $unlimit_danger_detections[0] : null;
                    break;
                case config('const.top_block_type_codes')['detect_list_danger']:
                    if (!isset($danger_rules)) {
                        $danger_rules = DangerService::getAllRules()->get()->all();
                    }
                    $request['starttime'] = date('Y-m-d', strtotime('-1 week'));
                    $request['endtime'] = date('Y-m-d');
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['starttime']) && $options['starttime'] != '') {
                            $request['starttime'] = $options['starttime'];
                        }
                        if (isset($options['endtime']) && $options['endtime'] != '') {
                            $request['endtime'] = $options['endtime'];
                        }
                        if (isset($options['selected_rules'])){
                            $request['selected_rules'] = $options['selected_rules'];
                        }
                    }
                    $list_danger_detections = DangerService::searchDetections($request)->get()->toArray();
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rules = isset($request['selected_rules']) ? $request['selected_rules'] : [];
                    $item->danger_detections = $list_danger_detections;
                    $item->rules = $danger_rules;
                    break;
                case config('const.top_block_type_codes')['live_graph_danger']:
                    if (!isset($danger_cameras)) {
                        $danger_cameras = DangerService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($danger_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            $temp[] = $camera;
                        }
                        $danger_cameras = $temp;
                    }
                    $item->cameras = $danger_cameras;

                    $item->selected_camera = null;
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['selected_camera']) && $options['selected_camera'] > 0) {
                            $request['selected_camera'] = $options['selected_camera'];
                            foreach ($item->cameras as $camera_item) {
                                if ($camera_item['id'] == $options['selected_camera']) {
                                    $item->selected_camera = $camera_item;
                                }
                            }
                        }
                        if (isset($options['time_period']) && $options['time_period']) {
                            $item->time_period = $options['time_period'];
                        }
                    } else {
                        $item->selected_camera = count($item->cameras) > 0 ? $item->cameras[0] : null;
                    }

                    $request['starttime'] = date('Y-m-d');
                    $request['endtime'] = date('Y-m-d');
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['starttime']) && $options['starttime'] != '') {
                            $request['starttime'] = $options['starttime'];
                        }
                        if (isset($options['endtime']) && $options['endtime'] != '') {
                            $request['endtime'] = $options['endtime'];
                        }
                    }
                    $live_danger_detections = DangerService::searchDetections($request)->get()->toArray();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];

                    $all_data = [];
                    foreach (array_reverse($live_danger_detections) as $danger_detection_item) {
                        if ($danger_detection_item['detection_action_id'] > 0) {
                            $all_data[date('Y-m-d H:i:00', strtotime($danger_detection_item['starttime']))][$danger_detection_item['detection_action_id']][] = $danger_detection_item;
                        }
                    }
                    $item->danger_live_graph_data = $all_data;

                    break;
                case config('const.top_block_type_codes')['past_graph_danger']:
                    if (!isset($danger_rules)) {
                        $danger_rules = DangerService::getAllRules()->get()->all();
                    }
                    if (count($danger_rules) > 0){
                        $request['selected_rule'] = $danger_rules[0]->id;
                    }
                    if (!isset($danger_cameras)) {
                        $danger_cameras = DangerService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($danger_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            $temp[] = $camera;
                        }
                        $danger_cameras = $temp;
                    }
                    $item->cameras = $danger_cameras;
                    $item->selected_camera = null;
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['selected_camera']) && $options['selected_camera'] > 0) {
                            $request['selected_camera'] = $options['selected_camera'];
                            foreach ($item->cameras as $camera_item) {
                                if ($camera_item['id'] == $options['selected_camera']) {
                                    $item->selected_camera = $camera_item;
                                }
                            }
                        }
                        if (isset($options['time_period']) && $options['time_period']) {
                            $item->time_period = $options['time_period'];
                        }
                    } else {
                        $item->selected_camera = count($item->cameras) > 0 ? $item->cameras[0] : null;
                    }

                    $request['starttime'] = date('Y-m-d', strtotime('-1 week'));
                    $request['endtime'] = date('Y-m-d');
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['starttime']) && $options['starttime'] != '') {
                            $request['starttime'] = $options['starttime'];
                        }
                        if (isset($options['endtime']) && $options['endtime'] != '') {
                            $request['endtime'] = $options['endtime'];
                        }
                        if (isset($options['selected_rule']) && $options['selected_rule'] > 0){
                            $request['selected_rule'] = $options['selected_rule'];
                        }
                    }
                    $past_danger_detections = DangerService::searchDetections($request)->get()->toArray();
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rule = $request['selected_rule'];
                    $item->rules = $danger_rules;

                    $all_data = [];
                    foreach ($past_danger_detections as $danger_detection_item) {
                        if ($danger_detection_item['detection_action_id'] > 0) {
                            $all_data[date('Y-m-d H:i', strtotime($danger_detection_item['starttime']))][$danger_detection_item['detection_action_id']][] = $danger_detection_item;
                        }
                    }
                    $item->danger_past_graph_data = $all_data;
                    break;
                case config('const.top_block_type_codes')['live_video_pit']:
                    if (!isset($pit_cameras)) {
                        $pit_cameras = PitService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($pit_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            $temp[] = $camera;
                        }
                        $pit_cameras = $temp;
                    }
                    $item->cameras = $pit_cameras;

                    $item->selected_camera = null;
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['selected_camera']) && $options['selected_camera'] > 0) {
                            $request['selected_camera'] = $options['selected_camera'];
                            foreach ($item->cameras as $camera_item) {
                                if ($camera_item['id'] == $options['selected_camera']) {
                                    $item->selected_camera = $camera_item;
                                }
                            }
                        }
                    } else {
                        $item->selected_camera = count($item->cameras) > 0 ? $item->cameras[0] : null;
                    }
                    $item->rules = null;
                    if ($item->selected_camera != null) {
                        $item->rules = PitService::getRulesByCameraID($item->selected_camera['id'])->toArray();
                    }
                    break;
                case config('const.top_block_type_codes')['recent_detect_pit']:
                case config('const.top_block_type_codes')['pit_history']:
                    if (!isset($pit_cameras)) {
                        $pit_cameras = PitService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($pit_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            $temp[] = $camera;
                        }
                        $pit_cameras = $temp;
                    }
                    $item->cameras = $pit_cameras;

                    $item->selected_camera = null;
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['selected_camera']) && $options['selected_camera'] > 0) {
                            $request['selected_camera'] = $options['selected_camera'];
                            foreach ($item->cameras as $camera_item) {
                                if ($camera_item['id'] == $options['selected_camera']) {
                                    $item->selected_camera = $camera_item;
                                }
                            }
                        }
                    } else {
                        $item->selected_camera = count($item->cameras) > 0 ? $item->cameras[0] : null;
                    }
                    $request['starttime'] = date('Y-m-d');
                    $request['endtime'] = date('Y-m-d');
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['starttime']) && $options['starttime'] != '') {
                            $request['starttime'] = $options['starttime'];
                        }
                        if (isset($options['endtime']) && $options['endtime'] != '') {
                            $request['endtime'] = $options['endtime'];
                        }
                    }
                    $unlimit_pit_detections = PitService::searchDetections($request)->get()->all();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $pit_over_data = PitService::extractOverData(array_reverse($unlimit_pit_detections));
                    $pit_over_data = array_reverse($pit_over_data);
                    $item->pit_detections = array_reverse($unlimit_pit_detections);
                    $item->pit_detection = count($pit_over_data) > 0 ? $pit_over_data[0] : null;
                    break;
                case config('const.top_block_type_codes')['detect_list_pit']:
                    if (!isset($pit_rules)) {
                        $pit_rules = PitService::getAllRules()->get()->all();
                    }
                    $request['starttime'] = date('Y-m-d', strtotime('-1 week'));
                    $request['endtime'] = date('Y-m-d');
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['starttime']) && $options['starttime'] != '') {
                            $request['starttime'] = $options['starttime'];
                        }
                        if (isset($options['endtime']) && $options['endtime'] != '') {
                            $request['endtime'] = $options['endtime'];
                        }
                        if (isset($options['selected_rules'])){
                            $request['selected_rules'] = $options['selected_rules'];
                        }
                    }
                    $list_pit_detections = PitService::searchDetections($request)->get()->all();
                    $list_pit_detections = PitService::extractOverData(array_reverse($list_pit_detections));
                    $list_pit_detections = array_reverse($list_pit_detections);
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rules = isset($request['selected_rules']) ? $request['selected_rules'] : [];
                    $item->rules = $pit_rules;
                    $item->pit_detections = $list_pit_detections;
                    break;
                case config('const.top_block_type_codes')['live_graph_pit']:
                    if (!isset($pit_cameras)) {
                        $pit_cameras = PitService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($pit_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            $temp[] = $camera;
                        }
                        $pit_cameras = $temp;
                    }
                    $item->cameras = $pit_cameras;

                    $item->selected_camera = null;
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['selected_camera']) && $options['selected_camera'] > 0) {
                            $request['selected_camera'] = $options['selected_camera'];
                            foreach ($item->cameras as $camera_item) {
                                if ($camera_item['id'] == $options['selected_camera']) {
                                    $item->selected_camera = $camera_item;
                                }
                            }
                        }
                        if (isset($options['time_period']) && $options['time_period']) {
                            $item->time_period = $options['time_period'];
                        }
                    } else {
                        $item->selected_camera = count($item->cameras) > 0 ? $item->cameras[0] : null;
                        $request['selected_camera'] = count($item->cameras) > 0 ? $item->cameras[0]['id'] : null;
                    }
                    $live_pit_detections = PitService::searchDetections($request, true)->get()->all();

                    $total_data = [];
                    foreach (array_reverse($live_pit_detections) as $pit_item) {
                        if (!isset($total_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))])) {
                            $total_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))] = $pit_item->nb_entry - $pit_item->nb_exit;
                        } else {
                            $delta = 1;
                            while (isset($total_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)])) {
                                ++$delta;
                            }
                            $total_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)] = $pit_item->nb_entry - $pit_item->nb_exit;
                        }
                    }
                    $item->pit_live_graph_data = $total_data;
                    break;
                case config('const.top_block_type_codes')['past_graph_pit']:
                    if (!isset($pit_rules)) {
                        $pit_rules = PitService::getAllRules()->get()->all();
                    }
                    if (count($pit_rules) > 0){
                        $request['selected_rule'] = $pit_rules[0]->id;
                    }

                    $request['starttime'] = date('Y-m-d');
                    $request['endtime'] = date('Y-m-d');
                    if ($item->options != null) {
                        $options = (array) json_decode($item->options);
                        if (isset($options['starttime']) && $options['starttime'] != '') {
                            $request['starttime'] = $options['starttime'];
                        }
                        if (isset($options['endtime']) && $options['endtime'] != '') {
                            $request['endtime'] = $options['endtime'];
                        }
                        if (isset($options['time_period']) && $options['time_period']) {
                            $item->time_period = $options['time_period'];
                        }
                        if (isset($options['selected_rule']) && $options['selected_rule']) {
                            $request['selected_rule'] = $options['selected_rule'];
                        }
                    }
                    $past_pit_detections = PitService::searchDetections($request)->get()->all();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rule = $request['selected_rule'];
                    $item->rules = $pit_rules;

                    $total_data = [];
                    foreach (array_reverse($past_pit_detections) as $pit_item) {
                        if (!isset($total_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))])) {
                            $total_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))] = $pit_item->nb_entry - $pit_item->nb_exit;
                        } else {
                            $delta = 1;
                            while (isset($total_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)])) {
                                ++$delta;
                            }
                            $total_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)] = $pit_item->nb_entry - $pit_item->nb_exit;
                        }
                    }
                    $item->pit_past_graph_data = $total_data;
                    break;
            }
        }

        return view('admin.top.index')->with([
            'top_blocks' => $top_blocks,
            'scroll_top' => isset($request_params['scroll_top']) && $request_params['scroll_top'] !== '' ? $request_params['scroll_top'] : null,
        ]);
    }

    public function AjaxUpdate(Request $request)
    {
        if (isset($request['changed_data']) && count($request['changed_data']) > 0) {
            foreach ($request['changed_data'] as $item) {
                if (isset($item['id']) && $item['id'] > 0) {
                    $top_block = TopBlock::find($item['id']);
                    if (isset($item['gs_x'])) {
                        $top_block->gs_x = $item['gs_x'];
                    }
                    if (isset($item['gs_y'])) {
                        $top_block->gs_y = $item['gs_y'];
                    }
                    if (isset($item['gs_w'])) {
                        $top_block->gs_w = $item['gs_w'];
                    }
                    if (isset($item['gs_h'])) {
                        $top_block->gs_h = $item['gs_h'];
                    }
                    if (isset($item['selected_camera']) && $item['selected_camera'] > 0) {
                        $options = $top_block->options;
                        if ($options != null) {
                            $options = (array) json_decode($options);
                            $options['selected_camera'] = $item['selected_camera'];
                        } else {
                            $options = ['selected_camera' => $item['selected_camera']];
                        }
                        $top_block->options = json_encode($options);
                    }
                    if (isset($item['selected_rule']) && $item['selected_rule'] > 0) {
                        $options = $top_block->options;
                        if ($options != null) {
                            $options = (array) json_decode($options);
                            $options['selected_rule'] = $item['selected_rule'];
                        } else {
                            $options = ['selected_rule' => $item['selected_rule']];
                        }
                        $top_block->options = json_encode($options);
                    }
                    if (isset($item['selected_rules'])) {
                        $options = $top_block->options;
                        if ($options != null) {
                            $options = (array) json_decode($options);
                            $options['selected_rules'] = $item['selected_rules'];
                        } else {
                            $options = ['selected_rules' => $item['selected_rules']];
                        }
                        $top_block->options = json_encode($options);
                    }
                    if (isset($item['starttime']) && $item['starttime'] != '') {
                        $options = $top_block->options;
                        if ($options != null) {
                            $options = (array) json_decode($options);
                            $options['starttime'] = date('Y-m-d', strtotime($item['starttime']));
                        } else {
                            $options = ['starttime' => date('Y-m-d', strtotime($item['starttime']))];
                        }
                        $top_block->options = json_encode($options);
                    }
                    if (isset($item['endtime']) && $item['endtime'] != '') {
                        $options = $top_block->options;
                        if ($options != null) {
                            $options = (array) json_decode($options);
                            $options['endtime'] = date('Y-m-d', strtotime($item['endtime']));
                        } else {
                            $options = ['endtime' => date('Y-m-d', strtotime($item['endtime']))];
                        }
                        $top_block->options = json_encode($options);
                    }
                    if (isset($item['options'])) {
                        $top_block->options = $item['options'];
                    }
                    $top_block->save();
                }
            }
        }

        return 'ok';
    }

    public function AjaxDelete(Request $request)
    {
        if (isset($request['id']) && $request['id'] > 0) {
            TopBlock::find($request['id'])->delete();

            return 'delete ok';
        }

        return 'delete failed';
    }

    public function CheckDetectData(Request $request)
    {
        if (isset($request['type']) && $request['type'] != '' && isset($request['endtime']) && $request['endtime'] != '') {
            $endtime = $request['endtime'];
            $now = date('Y-m-d');
            if (strtotime($endtime) < strtotime($now)) {
                return false;
            }
            $last_record_id = 0;
            if (isset($request['last_record_id']) && $request['last_record_id'] != '') {
                $last_record_id = (int) $request['last_record_id'];
            }
            switch ($request['type']) {
                case 'pit':
                    $params = null;
                    if (isset($request['camera_id']) && $request['camera_id'] > 0) {
                        $params = ['selected_camera' => (int) $request['camera_id']];
                    }
                    if (isset($request['rule_id']) && $request['rule_id'] > 0) {
                        $params = ['rule_id' => (int) $request['rule_id']];
                    }
                    $last_record = PitService::searchDetections($params)->get()->first();
                    if ($last_record != null && $last_record_id < $last_record->id && strtotime($last_record->starttime) > strtotime(date('Y-m-d'))) {
                        return 1;
                    }
                    break;
                case 'danger':
                    $params = null;
                    if (isset($request['camera_id']) && $request['camera_id'] > 0) {
                        $params = ['selected_camera' => (int) $request['camera_id']];
                    }
                    $last_record = DangerService::searchDetections($params)->get()->first();
                    if ($last_record != null && $last_record_id < $last_record->id && strtotime($last_record->starttime) > strtotime(date('Y-m-d'))) {
                        return 1;
                    }
                    break;
                case 'shelf':
                    break;
                case 'thief':
                    break;
            }
        }

        return 0;
    }

    public function save_block(Request $request)
    {
        $login_user = Auth::guard('admin')->user();
        $block_type = $request['block_type'];
        $options = isset($request['options']) ? $request['options'] : null;
        $top_blocks = TopService::search()->get()->all();

        $enable_add_flag = true;
        $x = 0;
        $y = 0;
        $last_item = null;
        foreach ($top_blocks as $item) {
            // if ($item->block_type == $block_type) {
            //     $enable_add_flag = false;
            //     break;
            // }
            if ($item->gs_y + $item->gs_h >= $y) {
                $y = $item->gs_y + $item->gs_h;
                $last_item = $item;
            }
        }
        if ($last_item != null) {
            foreach ($top_blocks as $item) {
                if ($last_item->gs_y + $last_item->gs_h == $item->gs_y + $item->gs_h) {
                    if ($last_item->gs_x < $item->gs_x) {
                        $last_item = $item;
                    }
                }
            }
            $x = $last_item->gs_x + $last_item->gs_w;
            $y = $last_item->gs_y;
            if ($x + 4 > 12) {
                $x = 0;
                $y = $last_item->gs_y + $last_item->gs_h;
            }
        }
        // if ($enable_add_flag) {
        $new_block = new TopBlock();
        $new_block->user_id = $login_user->id;
        $new_block->block_type = $block_type;
        $new_block->options = $options != null && count($options) > 0 ? json_encode($options) : null;
        $new_block->gs_x = $x;
        $new_block->gs_y = $y;
        $new_block->gs_w = 4;
        $new_block->gs_h = 3;
        $new_block->save();

        return 'ダッシュボードに追加しました。';
        // }

        // return 'すでに登録されています。';
    }

    public function save_search_option(Request $request)
    {
        TopService::save_search_option($request);
    }

    public function delete(Request $request, TopBlock $top)
    {
        if (TopService::doDelete($top)) {
            $request->session()->flash('success', '削除しました。');

            return redirect()->route('admin.top');
        } else {
            $request->session()->flash('error', '削除が失敗しました。');

            return redirect()->route('admin.top');
        }
    }

    public function permission_group()
    {
        $login_user = Auth::guard('admin')->user();
        $authority_groups = [];

        if ($login_user->authority_id == config('const.super_admin_code')) {
            $data = AuthorityGroup::all();
        } else {
            $data = AuthorityGroup::query()->where('contract_no', $login_user->contract_no)->get()->all();
        }

        foreach ($data as $item) {
            $authority_groups[$item->authority_id][$item->group_id] = $item->access_flag ? 1 : 0;
        }

        return view('admin.top.permission_group')->with([
            'authority_groups' => $authority_groups,
        ]);
    }

    public function permission_store(Request $request)
    {
        $login_user = Auth::guard('admin')->user();
        if ($login_user->authority_id == config('const.super_admin_code')) {
            $request->session()->flash('error', 'スーパー管理者は権限グループを変更出来ません。');

            return redirect()->route('admin.top.permission_group');
        }
        foreach (config('const.authorities') as $authority_id => $authority) {
            if ($authority_id == config('const.authorities_codes.admin')) {
                continue;
            }
            foreach (config('const.pages') as $details) {
                foreach ($details as $detail) {
                    $authority_group = AuthorityGroup::where([
                        'authority_id' => $authority_id,
                        'group_id' => $detail['id'],
                        'contract_no' => $login_user->contract_no,
                    ])->first();
                    if (isset($request['checkbox'.$authority_id.'_'.$detail['id']])) {
                        $value = 1;
                    } else {
                        $value = 0;
                    }
                    if ($authority_group) {
                        $authority_group->access_flag = $value;
                        $authority_group->save();
                    } else {
                        AuthorityGroup::create([
                            'authority_id' => $authority_id,
                            'group_id' => $detail['id'],
                            'access_flag' => $value,
                            'contract_no' => $login_user->contract_no,
                        ]);
                    }
                }
            }
        }
        $request->session()->flash('success', '登録しました。');

        return redirect()->route('admin.top.permission_group');
    }

    public function error(Request $request)
    {
        return view('admin.top.error')->with([
            'error_code' => isset($request['error_code']) && $request['error_code'] !== '' ? $request['error_code'] : null,
        ]);
    }
}
