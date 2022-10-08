<?php

namespace App\Service;

use App\Models\Camera;
use App\Models\PitDetectionRule;
use App\Models\PitDetection;
use Illuminate\Support\Facades\Auth;

class PitService
{
    public static function doSearch($params = null)
    {
        $pit_rules = PitDetectionRule::select(
            'pit_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as camera_no',
            'locations.name as location_name'
        )->leftJoin('cameras', 'cameras.id', '=', 'pit_detection_rules.camera_id')
        ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $pit_rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }
        if ($params != null) {
            if (isset($params['selected_cameras']) && !is_array($params['selected_cameras']) && $params['selected_cameras'] != '') {
                $selected_cameras = json_decode($params['selected_cameras']);
                $pit_rules->whereIn('pit_detection_rules.camera_id', $selected_cameras);
            }
            if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
                $pit_rules->whereIn('pit_detection_rules.camera_id', $params['selected_cameras']);
            }
            if (isset($params['selected_camera']) && $params['selected_camera'] > 0) {
                $pit_rules->where('pit_detection_rules.camera_id', $params['selected_camera']);
            }
            if (isset($params) && $params->has('location') && $params->location > 0) {
                $pit_rules->where('cameras.location_id', $params->location);
            }
            if (isset($params) && $params->has('installation_position') && $params->installation_position != '') {
                $pit_rules->where('cameras.installation_position', 'like', '%'.$params->installation_position.'%');
            }
            if (isset($params) && $params->has('floor_number') && $params->floor_number != '') {
                $pit_rules->leftJoin('camera_mapping_details as map', 'cameras.id', 'map.camera_id')->whereNull('map.deleted_at')
                    ->leftJoin('location_drawings as drawing', 'drawing.id', 'map.drawing_id')->whereNull('drawing.deleted_at')
                    ->where('drawing.floor_number', 'LIKE', '%'.$params->floor_number.'%');
            }
        }

        return $pit_rules;
    }

    public static function saveData($params)
    {
        $change_flag = $params['change_flag'];
        $red_points_data = $params['red_points_data'];
        $blue_points_data = $params['blue_points_data'];
        $camera_id = $params['camera_id'];
        $name = isset($params['name']) && $params['name'] != '' ? $params['name'] : null;
        $max_permission_time = isset($params['max_permission_time']) && $params['max_permission_time'] > 0 ? $params['max_permission_time'] : null;
        $min_members = isset($params['min_members']) && $params['min_members'] > 0 ? $params['min_members'] : null;

        if ($change_flag == 'updated') {
            PitDetectionRule::query()->where('camera_id', $camera_id)->delete();
        } else {
            $cur_pit = self::getRulesByCameraID($camera_id)->first();
            if ($cur_pit != null) {
                if ($cur_pit->max_permission_time != $max_permission_time || $cur_pit->min_members != $min_members) {
                    PitDetectionRule::query()->where('camera_id', $camera_id)->delete();
                } else {
                    $cur_pit->red_points = $red_points_data;
                    $cur_pit->blue_points = $blue_points_data;
                    $cur_pit->max_permission_time = $max_permission_time;
                    $cur_pit->min_members = $min_members;
                    $cur_pit->name = $name;
                    $cur_pit->updated_by = Auth::guard('admin')->user()->id;
                    $res = $cur_pit->save();
                    if (!$res) {
                        return false;
                    }

                    return true;
                }
            }
        }

        $new_pit = new PitDetectionRule();
        $new_pit->name = $name;
        $new_pit->camera_id = $camera_id;
        $new_pit->red_points = $red_points_data;
        $new_pit->blue_points = $blue_points_data;
        $new_pit->max_permission_time = $max_permission_time;
        $new_pit->min_members = $min_members;
        $new_pit->created_by = Auth::guard('admin')->user()->id;
        $new_pit->updated_by = Auth::guard('admin')->user()->id;

        $res = $new_pit->save();
        if (!$res) {
            return false;
        }

        return true;
    }

    public static function doDelete($cur_pit)
    {
        if (is_object($cur_pit)) {
            return $cur_pit->delete();
        } else {
            abort(403);
        }
    }

    public static function getPitInfoById($id)
    {
        return PitDetectionRule::query()->where('id', $id)->get();
    }

    public static function getRulesByCameraID($camera_id)
    {
        return PitDetectionRule::query()->where('camera_id', $camera_id)->get();
    }

    public static function getCameraByRuleID($rule_id)
    {
        $res = null;
        $rule_data = self::getPitInfoById($rule_id)->first();
        if (isset($rule_data)) {
            $camera_id = $rule_data->camera_id;
            $res = CameraService::getCameraInfoById($camera_id);
        }

        return $res;
    }

    public static function searchDetections($params, $today_flag = false)
    {
        $query = PitDetection::query()
            ->select(
                'pit_detections.*',
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.contract_no',
                'cameras.camera_id as camera_no',
                'locations.name as location_name',
                'pit_detection_rules.max_permission_time',
                'pit_detection_rules.min_members'
            )
            ->leftJoin('pit_detection_rules', 'pit_detection_rules.id', 'pit_detections.rule_id')
            ->leftJoin('cameras', 'cameras.id', 'pit_detections.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if ($params != null) {
            if ($today_flag == true) {
                $query->whereDate('pit_detections.starttime', date('Y-m-d'));
            } else {
                if (isset($params['starttime']) && $params['starttime'] != '') {
                    $query->whereDate('pit_detections.starttime', '>=', $params['starttime']);
                } else {
                    $query->whereDate('pit_detections.starttime', '>=', date('Y-m-d'));
                }
                if (isset($params['endtime']) && $params['endtime'] != '') {
                    $query->whereDate('pit_detections.starttime', '<=', $params['endtime']);
                } else {
                    $query->whereDate('pit_detections.starttime', '<=', date('Y-m-d'));
                }
            }

            if (isset($params['rule_ids']) && $params['rule_ids'] != '') {
                $rule_ids = json_decode($params['rule_ids']);
                if (count($rule_ids) > 0) {
                    $query->whereIn('pit_detections.rule_id', $rule_ids);
                }
            }
            if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
                $query->whereIn('pit_detection_rules.camera_id', $params['selected_cameras']);
            }
            if (isset($params['selected_camera']) && $params['selected_camera'] > 0) {
                $query->where('pit_detection_rules.camera_id', $params['selected_camera']);
            }
        } else {
            if ($today_flag == true) {
                $query->whereDate('pit_detections.starttime', date('Y-m-d'));
            }
        }

        if (Auth::guard('admin')->user()->contract_no != null) {
            $query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $query->orderByDesc('pit_detections.starttime');

        return $query;
    }

    public static function getAllCameras()
    {
        $camera_ids = PitDetectionRule::query()->distinct('camera_id')->pluck('camera_id');
        $camera_query = Camera::query()->whereIn('cameras.id', $camera_ids)->select('cameras.*', 'locations.name as location_name');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $camera_query->leftJoin('locations', 'locations.id', 'cameras.location_id');

        return $camera_query->get()->all();
    }

    public static function extractOverData($data)
    {
        $temp = [];
        $sum_in_pit = [];
        $max_permission_time = [];
        $min_members = [];
        $over_start_time = [];
        $last_over_record = [];
        foreach ($data as $index => $item) {
            $nb_entry = $item->nb_entry;
            $nb_exit = $item->nb_exit;
            if (!isset($sum_in_pit[$item->rule_id])) {
                $sum_in_pit[$item->rule_id] = 0;
            }
            if (!isset($max_permission_time[$item->rule_id])) {
                $max_permission_time[$item->rule_id] = $item->max_permission_time;
            }
            if (!isset($min_members[$item->rule_id])) {
                $min_members[$item->rule_id] = $item->min_members;
            }
            $sum_in_pit[$item->rule_id] += ($nb_entry - $nb_exit);
            if ($sum_in_pit[$item->rule_id] >= $min_members[$item->rule_id]) {
                if (!isset($over_start_time[$item->rule_id])) {
                    $over_start_time[$item->rule_id] = $item->starttime;
                }
                $item->sum_in_pit = $sum_in_pit[$item->rule_id];
                if (strtotime($item->starttime) - strtotime($over_start_time[$item->rule_id]) >= $max_permission_time[$item->rule_id] * 60) {
                    $temp[] = $item;
                }
                $last_over_record[$item->rule_id] = $item;
            } else {
                if (isset($over_start_time[$item->rule_id])) {
                    if (strtotime($item->starttime) - strtotime($over_start_time[$item->rule_id]) >= $max_permission_time[$item->rule_id] * 60) {
                        if (isset($last_over_record[$item->rule_id]) && !in_array($last_over_record[$item->rule_id], $temp)) {
                            $temp[] = $last_over_record[$item->rule_id];
                        }
                    }
                    $over_start_time[$item->rule_id] = null;
                }
            }

            // if ($index == 0 && ($nb_entry - $nb_exit < 0)) {
            //     continue;
            // }
            // if ($nb_entry - $nb_exit > 0) {
            //     if ($index == count($data) - 1) {
            //         continue;
            //     }
            //     $increatment = 1;
            //     $cond_flag = true;
            //     do {
            //         if (isset($data[$index + $increatment])) {
            //             $next_item = $data[$index + $increatment];
            //             if ($rule_id == $next_item->rule_id) {
            //                 if ($next_item->nb_entry - $next_item->nb_exit < 0) {
            //                     if (strtotime($next_item->starttime) - strtotime($item->starttime) > $max_permission_time * 60) {
            //                         $temp[] = $item;
            //                         // $temp[] = $next_item;
            //                     }
            //                     $cond_flag = false;
            //                 }
            //             }
            //         } else {
            //             $cond_flag = false;
            //         }
            //         ++$increatment;
            //     } while ($cond_flag);
            // }
        }
        foreach ($last_over_record as $last_item) {
            if (!in_array($last_item, $temp)) {
                $temp[] = $last_item;
            }
        }

        return $temp;
    }
}
