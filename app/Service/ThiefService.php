<?php

namespace App\Service;

use App\Models\ThiefDetectionRule;
use App\Models\Camera;
use App\Models\ThiefDetection;
use Illuminate\Support\Facades\Auth;

class ThiefService
{
    public static function doSearch($params = null)
    {
        $rules = ThiefDetectionRule::select(
            'thief_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as device_id',
            'cameras.serial_no',
            'locations.name as location_name'
        )->leftJoin('cameras', 'cameras.id', '=', 'thief_detection_rules.camera_id')
        ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }
        if ($params != null) {
            if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')){
                $rules -> where(function($q) {
                    $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                    $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                    $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
                });
            }
            if (isset($params['selected_cameras']) && !is_array($params['selected_cameras']) && $params['selected_cameras'] != '') {
                $selected_cameras = json_decode($params['selected_cameras']);
                $rules->whereIn('thief_detection_rules.camera_id', $selected_cameras);
            }
            if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
                $rules->whereIn('thief_detection_rules.camera_id', $params['selected_cameras']);
            }
            if (isset($params['selected_camera']) && $params['selected_camera'] > 0) {
                $rules->where('thief_detection_rules.camera_id', $params['selected_camera']);
            }
            if (isset($params) && $params->has('location') && $params->location > 0) {
                $rules->where('cameras.location_id', $params->location);
            }
            if (isset($params) && $params->has('installation_position') && $params->installation_position != '') {
                $rules->where('cameras.installation_position', 'like', '%'.$params->installation_position.'%');
            }
            if (isset($params) && $params->has('floor_number') && $params->floor_number != '') {
                $rules->leftJoin('camera_mapping_details as map', 'cameras.id', 'map.camera_id')->whereNull('map.deleted_at')
                    ->leftJoin('location_drawings as drawing', 'drawing.id', 'map.drawing_id')->whereNull('drawing.deleted_at')
                    ->where('drawing.floor_number', 'LIKE', '%'.$params->floor_number.'%');
            }
        }

        return $rules;
    }

    public static function getAllCameras()
    {
        $camera_ids = ThiefDetectionRule::query()->distinct('camera_id')->pluck('camera_id');
        $camera_query = Camera::query()->whereIn('cameras.id', $camera_ids)->select('cameras.*', 'locations.name as location_name');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $camera_query->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')){
            $camera_query -> where(function($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
        }
        return $camera_query->get()->all();
    }

    public static function saveData($params)
    {
        $camera_id = $params['camera_id'];
        $rule_data = $params['rule_data'];
        $rule_data = json_decode($rule_data);
        if (count((array) $rule_data) > 0) {
            // ThiefDetectionRule::query()->where('camera_id', $camera_id)->delete();
            $unchanged_ids = [];
            foreach ($rule_data as $rule) {
                if (isset($rule->id) && $rule->id > 0) {
                    if (!(isset($rule->is_changed) && $rule->is_changed == true)) {
                        $unchanged_ids[] = $rule->id;
                        if (isset($rule->is_name_color_changed) && $rule->is_name_color_changed == true) {
                            $cur_rule = ThiefDetectionRule::find($rule->id);
                            $cur_rule->name = isset($rule->name) && $rule->name != '' ? $rule->name : null;
                            $cur_rule->color = $rule->color;
                            $cur_rule->save();
                        }
                    }
                }
            }
            ThiefDetectionRule::query()->where('camera_id', $camera_id)->whereNotIn('id', $unchanged_ids)->delete();
            foreach ($rule_data as $rule) {
                if (isset($rule->id) && $rule->id > 0 && !(isset($rule->is_changed) && $rule->is_changed == true)) {
                    continue;
                }
                if (is_array($rule->points) && count($rule->points) > 0) {
                    $new_rule = new ThiefDetectionRule();
                    $new_rule->color = $rule->color;
                    $new_rule->name = isset($rule->name) && $rule->name != '' ? $rule->name : null;
                    $new_rule->camera_id = $camera_id;
                    $new_rule->points = json_encode($rule->points);
                    $new_rule->hanger = $rule->hanger;
                    $new_rule->created_by = Auth::guard('admin')->user()->id;
                    $new_rule->updated_by = Auth::guard('admin')->user()->id;
                    $res = $new_rule->save();
                    if (!$res) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public static function doDelete($cur_rule)
    {
        if (is_object($cur_rule)) {
            return $cur_rule->delete();
        } else {
            abort(403);
        }
    }

    public static function getThiefRuleInfoById($id)
    {
        return ThiefDetectionRule::query()->where('id', $id)->get();
    }

    public static function getRulesByCameraID($camera_id)
    {
        return ThiefDetectionRule::query()->where('camera_id', $camera_id)->get();
    }

    public static function getCameraByRuleID($rule_id)
    {
        $res = null;
        $rule_data = self::getThiefRuleInfoById($rule_id)->first();
        if (isset($rule_data)) {
            $camera_id = $rule_data->camera_id;
            $res = CameraService::getCameraInfoById($camera_id);
        }

        return $res;
    }

    public static function searchDetections($params)
    {
        $query = ThiefDetection::query()
            ->select(
                'thief_detections.*',
                'thief_detection_rules.color',
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.contract_no',
                'cameras.camera_id as device_id',
                'cameras.serial_no',
                'locations.name as location_name'
            )
            ->leftJoin('thief_detection_rules', 'thief_detection_rules.id', 'thief_detections.rule_id')
            ->leftJoin('cameras', 'cameras.id', 'thief_detections.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (isset($params['starttime']) && $params['starttime'] != '') {
            $query->whereDate('thief_detections.starttime', '>=', $params['starttime']);
        } else {
            $query->whereDate('thief_detections.starttime', '>=', date('Y-m-d', strtotime('-1 week')));
        }
        if (isset($params['endtime']) && $params['endtime'] != '') {
            $query->whereDate('thief_detections.starttime', '<=', $params['endtime']);
        } else {
            $query->whereDate('thief_detections.starttime', '<=', date('Y-m-d'));
        }
        if (isset($params['rule_ids']) && $params['rule_ids'] != '') {
            $rule_ids = json_decode($params['rule_ids']);
            if (count($rule_ids) > 0) {
                $query->whereIn('thief_detections.rule_id', $rule_ids);
            }
        }
        if (isset($params['selected_rules']) && is_array($params['selected_rules']) && count($params['selected_rules']) > 0) {
            $query->whereIn('thief_detections.rule_id', $params['selected_rules']);
        }
        if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
            $query->whereIn('thief_detections.camera_id', $params['selected_cameras']);
        }
        if (Auth::guard('admin')->user()->contract_no != null) {
            $query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')){
            $query -> where(function($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
        }
        $query->orderByDesc('thief_detections.starttime');

        return $query;
    }
}
