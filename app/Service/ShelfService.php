<?php

namespace App\Service;

use App\Models\ShelfDetectionRule;
use App\Models\Camera;
use App\Models\ShelfDetection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShelfService
{
    public static function doSearch($params = null)
    {
        $rules = ShelfDetectionRule::select(
            'shelf_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as device_id',
            'cameras.serial_no',
            'locations.name as location_name'
        )->leftJoin('cameras', 'cameras.id', '=', 'shelf_detection_rules.camera_id')
        ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }
        if ($params != null) {
            if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')) {
                $rules->where(function ($q) {
                    $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                    $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                    $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
                });
            }
            if (isset($params['selected_cameras']) && !is_array($params['selected_cameras']) && $params['selected_cameras'] != '') {
                $selected_cameras = json_decode($params['selected_cameras']);
                $rules->whereIn('shelf_detection_rules.camera_id', $selected_cameras);
            }
            if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
                $rules->whereIn('shelf_detection_rules.camera_id', $params['selected_cameras']);
            }
            if (isset($params['selected_camera']) && $params['selected_camera'] > 0) {
                $rules->where('shelf_detection_rules.camera_id', $params['selected_camera']);
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

    public static function getAllRules()
    {
        $rules = DB::table('shelf_detection_rules')
            ->select(
                'shelf_detection_rules.*',
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.camera_id as device_id',
                'cameras.serial_no',
                'cameras.contract_no',
                'locations.name as location_name'
            )->leftJoin('cameras', 'cameras.id', '=', 'shelf_detection_rules.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id')
            ->whereIn('shelf_detection_rules.id', function ($q) {
                $q->select('rule_id')->from('shelf_detections');
            });
        if (Auth::guard('admin')->user()->contract_no != null) {
            $rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')) {
            $rules->where(function ($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
        }

        $rules->orderByRaw('-shelf_detection_rules.deleted_at', 'DESC')->orderByDesc('shelf_detection_rules.updated_at');

        return $rules;
    }

    public static function getAllCameras()
    {
        $camera_ids = ShelfDetectionRule::query()->distinct('camera_id')->pluck('camera_id');
        $camera_query = Camera::query()->whereIn('cameras.id', $camera_ids)->select('cameras.*', 'locations.name as location_name');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $camera_query->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')) {
            $camera_query->where(function ($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
        }

        return $camera_query->get();
    }

    public static function saveData($params)
    {
        $camera_id = $params['camera_id'];
        $hour = $params['hour'];
        $mins = $params['mins'];
        $rule_data = json_decode($params['rule_data']);
        // ShelfDetectionRule::query()->where('camera_id', $camera_id)->delete();
        $unchanged_ids = [];
        foreach ($rule_data as $rule_item) {
            if (isset($rule_item->id) && $rule_item->id > 0) {
                $cur_rule = ShelfDetectionRule::find($rule_item->id);
                if ($cur_rule == null) {
                    return '編集中にルールが変更されたため登録出来ませんでした';
                }
                if (!(isset($rule_item->is_changed) && $rule_item->is_changed == true)) {
                    $unchanged_ids[] = $rule_item->id;
                    // if (isset($rule_item->is_name_color_changed) && $rule_item->is_name_color_changed == true) {
                    $cur_rule->name = isset($rule_item->name) && $rule_item->name != '' ? $rule_item->name : null;
                    $cur_rule->color = $rule_item->color;
                    $cur_rule->hour = $hour;
                    $cur_rule->mins = $mins;
                    $cur_rule->save();
                    // }
                }
            }
        }
        ShelfDetectionRule::query()->where('camera_id', $camera_id)->whereNotIn('id', $unchanged_ids)->delete();
        foreach ($rule_data as $rule) {
            if (isset($rule->id) && $rule->id > 0 && !(isset($rule->is_changed) && $rule->is_changed == true)) {
                continue;
            }
            if (is_array($rule->points)) {
                $new_rule = new ShelfDetectionRule();
                $new_rule->color = $rule->color;
                $new_rule->name = isset($rule->name) && $rule->name != '' ? $rule->name : null;
                $new_rule->camera_id = $camera_id;
                $new_rule->points = json_encode($rule->points);
                $new_rule->hour = $hour;
                $new_rule->mins = $mins;

                $new_rule->created_by = Auth::guard('admin')->user()->id;
                $new_rule->updated_by = Auth::guard('admin')->user()->id;
                $res = $new_rule->save();
                if (!$res) {
                    return false;
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

    public static function getShelfRuleInfoById($id)
    {
        return ShelfDetectionRule::query()->where('id', $id)->get();
    }

    public static function getRulesByCameraID($camera_id)
    {
        return ShelfDetectionRule::query()->where('camera_id', $camera_id)->get();
    }

    public static function getCameraByRuleID($rule_id)
    {
        $res = null;
        $rule_data = self::getShelfRuleInfoById($rule_id)->first();
        if (isset($rule_data)) {
            $camera_id = $rule_data->camera_id;
            $res = CameraService::getCameraInfoById($camera_id);
        }

        return $res;
    }

    public static function searchDetections($params)
    {
        $query = ShelfDetection::query()
            ->select(
                'shelf_detections.*',
                'shelf_detection_rules.color',
                'shelf_detection_rules.points',
                'shelf_detection_rules.name as rule_name',
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.contract_no',
                'cameras.camera_id as device_id',
                'cameras.serial_no',
                'locations.name as location_name'
            )
            ->leftJoin('shelf_detection_rules', 'shelf_detection_rules.id', 'shelf_detections.rule_id')
            ->leftJoin('cameras', 'cameras.id', 'shelf_detections.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (isset($params['starttime']) && $params['starttime'] != '') {
            $query->whereDate('shelf_detections.starttime', '>=', $params['starttime']);
        } else {
            $query->whereDate('shelf_detections.starttime', '>=', date('Y-m-d', strtotime('-1 week')));
        }
        if (isset($params['endtime']) && $params['endtime'] != '') {
            $query->whereDate('shelf_detections.starttime', '<=', $params['endtime']);
        } else {
            $query->whereDate('shelf_detections.starttime', '<=', date('Y-m-d'));
        }
        if (isset($params['rule_ids']) && $params['rule_ids'] != '') {
            $rule_ids = json_decode($params['rule_ids']);
            if (count($rule_ids) > 0) {
                $query->whereIn('shelf_detections.rule_id', $rule_ids);
            }
        }
        if (isset($params['selected_rules']) && is_array($params['selected_rules']) && count($params['selected_rules']) > 0) {
            $query->whereIn('shelf_detections.rule_id', $params['selected_rules']);
        }
        if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
            $query->whereIn('shelf_detections.camera_id', $params['selected_cameras']);
        }
        if (isset($params['selected_rule']) && $params['selected_rule'] > 0) {
            $query->where('shelf_detections.rule_id', $params['selected_rule']);
        }
        if (isset($params['selected_camera']) && $params['selected_camera'] > 0) {
            $query->where('shelf_detections.camera_id', $params['selected_camera']);
        }
        if (Auth::guard('admin')->user()->contract_no != null) {
            $query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')) {
            $query->where(function ($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
        }
        $query->orderByDesc('shelf_detections.starttime');

        return $query;
    }
}
