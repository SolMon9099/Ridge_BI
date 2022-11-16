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
use App\Service\ShelfService;
use App\Service\ThiefService;
use Illuminate\Support\Facades\Storage;
use App\Models\CameraMappingDetail;
use Illuminate\Support\Facades\DB;

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
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
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
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
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
                        $danger_rules = DangerService::getAllRules()->get()->toArray();
                        foreach ($danger_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
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
                        if (isset($options['selected_rules'])) {
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
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
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
                        $danger_rules = DangerService::getAllRules()->get()->toArray();
                        foreach ($danger_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
                    }
                    if (count($danger_rules) > 0) {
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
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
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
                        if (isset($options['selected_rule']) && $options['selected_rule'] > 0) {
                            $request['selected_rule'] = $options['selected_rule'];
                        }
                    }
                    $past_danger_detections = DangerService::searchDetections($request)->get()->toArray();
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rule = isset($request['selected_rule']) ? $request['selected_rule'] : null;
                    $item->rules = $danger_rules;

                    $all_data = [];
                    foreach (array_reverse($past_danger_detections) as $danger_detection_item) {
                        if ($danger_detection_item['detection_action_id'] > 0) {
                            $all_data[date('Y-m-d H:i:00', strtotime($danger_detection_item['starttime']))][$danger_detection_item['detection_action_id']][] = $danger_detection_item;
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
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
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
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
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
                        $pit_rules = PitService::getAllRules()->get()->toArray();
                        foreach ($pit_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
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
                        if (isset($options['selected_rules'])) {
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
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
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
                    $live_pit_detections = PitService::setSumInPit(array_reverse($live_pit_detections));
                    $graph_data = [];
                    foreach ($live_pit_detections as $pit_item) {
                        if (!isset($graph_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))])) {
                            $graph_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))] = $pit_item->sum_in_pit;
                        } else {
                            $delta = 1;
                            while (isset($graph_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)])) {
                                ++$delta;
                            }
                            $graph_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)] = $pit_item->sum_in_pit;
                        }
                    }
                    $item->pit_live_graph_data = $graph_data;
                    break;
                case config('const.top_block_type_codes')['past_graph_pit']:
                    if (!isset($pit_rules)) {
                        $pit_rules = PitService::getAllRules()->get()->toArray();
                        foreach ($pit_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
                    }
                    if (count($pit_rules) > 0) {
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
                    $past_pit_detections = PitService::setSumInPit(array_reverse($past_pit_detections));
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rule = isset($request['selected_rule']) ? $request['selected_rule'] : null;
                    $item->rules = $pit_rules;

                    $graph_data = [];
                    foreach ($past_pit_detections as $pit_item) {
                        if (!isset($graph_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))])) {
                            $graph_data[date('Y-m-d H:i:s', strtotime($pit_item->starttime))] = $pit_item->sum_in_pit;
                        } else {
                            $delta = 1;
                            while (isset($graph_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)])) {
                                ++$delta;
                            }
                            $graph_data[date('Y-m-d H:i:s.u', strtotime($pit_item->starttime) + $delta)] = $pit_item->sum_in_pit;
                        }
                    }
                    $item->pit_past_graph_data = $graph_data;
                    break;
                case config('const.top_block_type_codes')['live_video_shelf']:
                    if (!isset($shelf_cameras)) {
                        $shelf_cameras = ShelfService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($shelf_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $shelf_cameras = $temp;
                    }
                    $item->cameras = $shelf_cameras;

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
                        $item->rules = ShelfService::getRulesByCameraID($item->selected_camera['id'])->toArray();
                    }
                    break;
                case config('const.top_block_type_codes')['recent_detect_shelf']:
                    if (!isset($shelf_cameras)) {
                        $shelf_cameras = ShelfService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($shelf_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $shelf_cameras = $temp;
                    }
                    $item->cameras = $shelf_cameras;

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
                    $unlimit_shelf_detections = ShelfService::searchDetections($request)->get()->toArray();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->shelf_detections = $unlimit_shelf_detections;
                    $item->shelf_detection = count($unlimit_shelf_detections) > 0 ? $unlimit_shelf_detections[0] : null;
                    break;
                case config('const.top_block_type_codes')['detect_list_shelf']:
                    if (!isset($shelf_rules)) {
                        $shelf_rules = ShelfService::getAllRules()->get()->toArray();
                        foreach ($shelf_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
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
                        if (isset($options['selected_rules'])) {
                            $request['selected_rules'] = $options['selected_rules'];
                        }
                    }
                    $list_shelf_detections = ShelfService::searchDetections($request)->get()->toArray();
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rules = isset($request['selected_rules']) ? $request['selected_rules'] : [];
                    $item->shelf_detections = $list_shelf_detections;
                    $item->rules = $shelf_rules;
                    break;
                case config('const.top_block_type_codes')['live_graph_shelf']:
                    if (!isset($shelf_cameras)) {
                        $shelf_cameras = ShelfService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($shelf_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $shelf_cameras = $temp;
                    }
                    $item->cameras = $shelf_cameras;

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
                    $live_shelf_detections = ShelfService::searchDetections($request)->get()->toArray();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];

                    $all_data = [];
                    foreach (array_reverse($live_shelf_detections) as $shelf_detection_item) {
                        if (!isset($all_data[date('Y-m-d H:i:00', strtotime($shelf_detection_item['starttime']))])) {
                            $all_data[date('Y-m-d H:i:00', strtotime($shelf_detection_item['starttime']))] = 0;
                        }
                        ++$all_data[date('Y-m-d H:i:00', strtotime($shelf_detection_item['starttime']))];
                    }
                    $item->shelf_live_graph_data = $all_data;

                    break;
                case config('const.top_block_type_codes')['past_graph_shelf']:
                    if (!isset($shelf_rules)) {
                        $shelf_rules = ShelfService::getAllRules()->get()->toArray();
                        foreach ($shelf_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
                    }
                    if (count($shelf_rules) > 0) {
                        $request['selected_rule'] = $shelf_rules[0]->id;
                    }
                    if (!isset($shelf_cameras)) {
                        $shelf_cameras = ShelfService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($shelf_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $shelf_cameras = $temp;
                    }
                    $item->cameras = $shelf_cameras;
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
                        if (isset($options['selected_rule']) && $options['selected_rule'] > 0) {
                            $request['selected_rule'] = $options['selected_rule'];
                        }
                    }
                    $past_shelf_detections = ShelfService::searchDetections($request)->get()->toArray();
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rule = isset($request['selected_rule']) ? $request['selected_rule'] : null;
                    $item->rules = $shelf_rules;

                    $all_data = [];
                    foreach (array_reverse($past_shelf_detections) as $shelf_detection_item) {
                        if (!isset($all_data[date('Y-m-d H:i:00', strtotime($shelf_detection_item['starttime']))])) {
                            $all_data[date('Y-m-d H:i:00', strtotime($shelf_detection_item['starttime']))] = 0;
                        }
                        ++$all_data[date('Y-m-d H:i:00', strtotime($shelf_detection_item['starttime']))];
                    }
                    $item->shelf_past_graph_data = $all_data;
                    break;

                case config('const.top_block_type_codes')['live_video_thief']:
                    if (!isset($thief_cameras)) {
                        $thief_cameras = ThiefService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($thief_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $thief_cameras = $temp;
                    }
                    $item->cameras = $thief_cameras;

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
                        $item->rules = ThiefService::getRulesByCameraID($item->selected_camera['id'])->toArray();
                    }
                    break;
                case config('const.top_block_type_codes')['recent_detect_thief']:
                    if (!isset($thief_cameras)) {
                        $thief_cameras = ThiefService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($thief_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $thief_cameras = $temp;
                    }
                    $item->cameras = $thief_cameras;

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
                    $unlimit_thief_detections = ThiefService::searchDetections($request)->get()->toArray();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->thief_detections = $unlimit_thief_detections;
                    $item->thief_detection = count($unlimit_thief_detections) > 0 ? $unlimit_thief_detections[0] : null;
                    break;
                case config('const.top_block_type_codes')['detect_list_thief']:
                    if (!isset($thief_rules)) {
                        $thief_rules = ThiefService::getAllRules()->get()->toArray();
                        foreach ($thief_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
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
                        if (isset($options['selected_rules'])) {
                            $request['selected_rules'] = $options['selected_rules'];
                        }
                    }
                    $list_thief_detections = ThiefService::searchDetections($request)->get()->toArray();
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rules = isset($request['selected_rules']) ? $request['selected_rules'] : [];
                    $item->thief_detections = $list_thief_detections;
                    $item->rules = $thief_rules;
                    break;
                case config('const.top_block_type_codes')['live_graph_thief']:
                    if (!isset($thief_cameras)) {
                        $thief_cameras = ThiefService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($thief_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $thief_cameras = $temp;
                    }
                    $item->cameras = $thief_cameras;

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
                    $live_thief_detections = ThiefService::searchDetections($request)->get()->toArray();

                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];

                    $all_data = [];
                    foreach (array_reverse($live_thief_detections) as $thief_detection_item) {
                        if (!isset($all_data[date('Y-m-d H:i:00', strtotime($thief_detection_item['starttime']))])) {
                            $all_data[date('Y-m-d H:i:00', strtotime($thief_detection_item['starttime']))] = 0;
                        }
                        ++$all_data[date('Y-m-d H:i:00', strtotime($thief_detection_item['starttime']))];
                    }
                    $item->thief_live_graph_data = $all_data;

                    break;
                case config('const.top_block_type_codes')['past_graph_thief']:
                    if (!isset($thief_rules)) {
                        $thief_rules = ThiefService::getAllRules()->get()->toArray();
                        foreach ($thief_rules as &$rule) {
                            if (Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg')) {
                                $rule->is_on = true;
                            } else {
                                $rule->is_on = false;
                            }
                        }
                    }
                    if (count($thief_rules) > 0) {
                        $request['selected_rule'] = $thief_rules[0]->id;
                    }
                    if (!isset($thief_cameras)) {
                        $thief_cameras = ThiefService::getAllCameras()->toArray();
                        $access_tokens = [];
                        $temp = [];
                        foreach ($thief_cameras as $camera) {
                            if ($camera['contract_no'] == null) {
                                continue;
                            }
                            if (!in_array($camera['contract_no'], array_keys($access_tokens))) {
                                $safie_service = new SafieApiService($camera['contract_no']);
                                $access_tokens[$camera['contract_no']] = $safie_service->access_token;
                            }
                            $camera['access_token'] = $access_tokens[$camera['contract_no']];
                            if (Storage::disk('recent_camera_image')->exists($camera['camera_id'].'.jpeg')) {
                                $camera['is_on'] = true;
                            } else {
                                $camera['is_on'] = false;
                            }
                            $temp[] = $camera;
                        }
                        $thief_cameras = $temp;
                    }
                    $item->cameras = $thief_cameras;
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
                        if (isset($options['selected_rule']) && $options['selected_rule'] > 0) {
                            $request['selected_rule'] = $options['selected_rule'];
                        }
                    }
                    $past_thief_detections = ThiefService::searchDetections($request)->get()->toArray();
                    $item->starttime = $request['starttime'];
                    $item->endtime = $request['endtime'];
                    $item->selected_rule = isset($request['selected_rule']) ? $request['selected_rule'] : null;
                    $item->rules = $thief_rules;

                    $all_data = [];
                    foreach (array_reverse($past_thief_detections) as $thief_detection_item) {
                        if (!isset($all_data[date('Y-m-d H:i:00', strtotime($thief_detection_item['starttime']))])) {
                            $all_data[date('Y-m-d H:i:00', strtotime($thief_detection_item['starttime']))] = 0;
                        }
                        ++$all_data[date('Y-m-d H:i:00', strtotime($thief_detection_item['starttime']))];
                    }
                    $item->thief_past_graph_data = $all_data;
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

    public function AjaxGetRules(Request $request)
    {
        $res = '';
        $params = [];
        $type = $request['type'];
        switch ($type) {
            case 'pit':
                if (isset($request['camera_id']) && $request['camera_id'] > 0) {
                    $params['camera_id'] = $request['camera_id'];
                }
                $data = PitService::getAllRules($params)->get()->all();
                foreach ($data as $item) {
                    $map_data = CameraMappingDetail::select('drawing.floor_number')
                        ->where('camera_id', $item->camera_id)
                        ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                        // ->whereNull('drawing.deleted_at')
                        ->get()->first();
                    if ($map_data != null) {
                        $item->floor_number = $map_data->floor_number;
                    } else {
                        $item->floor_number = '';
                    }
                    $last_detection = DB::table('pit_detections')->where('rule_id', $item->id)->orderByDesc('starttime')->get()->first();
                    $item->img_path = $last_detection != null ? $last_detection->thumb_img_path : null;
                    $res .= '<tr>';
                    if ($request['page'] == 'list') {
                        $res .= '<td class="stick-t"><div class="checkbtn-wrap">';
                        $res .= '<input value="'.$item->id.'" class="rule_checkbox" type="checkbox" id="rule-'.$item->id.'"/>';
                        $res .= '<label for="rule-'.$item->id.'" class="custom-style"></label>';
                        $res .= '</div></td>';
                    } else {
                        $res .= '<td class="stick-t"><div class="checkbtn-wrap">';
                        $checked = '';
                        if (isset($request['selected_rule_id']) && $request['selected_rule_id'] > 0 && $request['selected_rule_id'] == $item->id) {
                            $checked = 'checked';
                        }
                        $res .= '<input name="selected_rule" value="'.$item->id.'" type="radio" '.$checked.' id="rule-'.$item->id.'"/>';
                        $res .= '<label for="rule-'.$item->id.'"></label>';
                        $res .= '</div></td>';
                    }

                    $res .= '<td>'.$item->name.'</td>';
                    $res .= '<td>'.$item->serial_no.'</td>';
                    $res .= '<td>'.$item->location_name.'</td>';
                    $res .= '<td>'.$item->floor_number.'</td>';
                    $res .= '<td>'.$item->installation_position.'</td>';
                    $res .= '<td>'.date('Y-m-d', strtotime($item->created_at)).'～'.($item->deleted_at != null ? date('Y-m-d', strtotime($item->deleted_at)) : '').'</td>';
                    $res .= '<td><img width="100px" src="'.asset('storage/thumb').'/'.$item->img_path.'"/></td>';
                    $res .= '<td><a class="rule-detail-link" onclick="location.href='."'".route('admin.pit.rule_view').'?id='.$item->id."'".'">ルール詳細>></a></td>';
                    $res .= '</tr>';
                }
                break;
            case 'danger':
                if (isset($request['camera_id']) && $request['camera_id'] > 0) {
                    $params['camera_id'] = $request['camera_id'];
                }
                if (isset($request['action_id']) && $request['action_id'] > 0) {
                    $params['action_id'] = $request['action_id'];
                }
                $data = DangerService::getAllRules($params)->get()->all();
                foreach ($data as $item) {
                    $map_data = CameraMappingDetail::select('drawing.floor_number')
                        ->where('camera_id', $item->camera_id)
                        ->leftJoin('location_drawings as drawing', 'drawing.id', 'drawing_id')
                        // ->whereNull('drawing.deleted_at')
                        ->get()->first();
                    if ($map_data != null) {
                        $item->floor_number = $map_data->floor_number;
                    } else {
                        $item->floor_number = '';
                    }
                    $last_detection = DB::table('danger_area_detections')->where('rule_id', $item->id)->orderByDesc('starttime')->get()->first();
                    $item->img_path = $last_detection != null ? $last_detection->thumb_img_path : null;
                    $res .= '<tr>';
                    if ($request['page'] == 'list') {
                        $res .= '<td class="stick-t"><div class="checkbtn-wrap">';
                        $res .= '<input value="'.$item->id.'" class="rule_checkbox" type="checkbox" id="rule-'.$item->id.'"/>';
                        $res .= '<label for="rule-'.$item->id.'" class="custom-style"></label>';
                        $res .= '</div></td>';
                    } else {
                        $res .= '<td class="stick-t"><div class="checkbtn-wrap">';
                        $checked = '';
                        if (isset($request['selected_rule_id']) && $request['selected_rule_id'] > 0 && $request['selected_rule_id'] == $item->id) {
                            $checked = 'checked';
                        }
                        $res .= '<input name="selected_rule" value="'.$item->id.'" type="radio" '.$checked.' id="rule-'.$item->id.'"/>';
                        $res .= '<label for="rule-'.$item->id.'"></label>';
                        $res .= '</div></td>';
                    }
                    $res .= '<td>'.$item->name.'</td>';
                    $res .= '<td>'.$item->serial_no.'</td>';
                    $res .= '<td>'.$item->location_name.'</td>';
                    $res .= '<td>'.$item->floor_number.'</td>';
                    $res .= '<td>'.$item->installation_position.'</td>';
                    $res .= '<td>';
                    foreach (json_decode($item->action_id) as $action_code) {
                        $res .= '<div>'.config('const.action')[$action_code].'</div>';
                    }
                    $res .= '</td>';
                    $res .= '<td><input disabled type="color" value = "'.$item->color.'"></td>';
                    $res .= '<td>'.date('Y-m-d', strtotime($item->created_at)).'～'.($item->deleted_at != null ? date('Y-m-d', strtotime($item->deleted_at)) : '').'</td>';
                    $res .= '<td><img width="100px" src="'.asset('storage/thumb').'/'.$item->img_path.'"/></td>';
                    $res .= '<td><a class="rule-detail-link" onclick="location.href='."'".route('admin.danger.rule_view').'?id='.$item->id."'".'">ルール詳細>></a></td>';
                    $res .= '</tr>';
                }
                break;
        }

        return $res;
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
                    if (isset($request['selected_rule']) && $request['selected_rule'] > 0) {
                        $params = ['selected_rule' => (int) $request['selected_rule']];
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
                    if (isset($request['selected_rule']) && $request['selected_rule'] > 0) {
                        $params = ['selected_rule' => (int) $request['selected_rule']];
                    }
                    $last_record = DangerService::searchDetections($params)->get()->first();
                    if ($last_record != null && $last_record_id < $last_record->id && strtotime($last_record->starttime) > strtotime(date('Y-m-d'))) {
                        return 1;
                    }
                    break;
                case 'shelf':
                    $params = null;
                    if (isset($request['camera_id']) && $request['camera_id'] > 0) {
                        $params = ['selected_camera' => (int) $request['camera_id']];
                    }
                    if (isset($request['selected_rule']) && $request['selected_rule'] > 0) {
                        $params = ['selected_rule' => (int) $request['selected_rule']];
                    }
                    $last_record = ShelfService::searchDetections($params)->get()->first();
                    if ($last_record != null && $last_record_id < $last_record->id && strtotime($last_record->starttime) > strtotime(date('Y-m-d'))) {
                        return 1;
                    }
                    break;
                case 'thief':
                    $params = null;
                    if (isset($request['camera_id']) && $request['camera_id'] > 0) {
                        $params = ['selected_camera' => (int) $request['camera_id']];
                    }
                    if (isset($request['selected_rule']) && $request['selected_rule'] > 0) {
                        $params = ['selected_rule' => (int) $request['selected_rule']];
                    }
                    $last_record = ThiefService::searchDetections($params)->get()->first();
                    if ($last_record != null && $last_record_id < $last_record->id && strtotime($last_record->starttime) > strtotime(date('Y-m-d'))) {
                        return 1;
                    }
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
