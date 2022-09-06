<?php

namespace App\Service;

use App\Models\ShelfDetectionRule;
use App\Models\ShelfDetection;
use Illuminate\Support\Facades\Auth;

class ShelfService
{
    public static function doSearch()
    {
        $rules = ShelfDetectionRule::select(
            'shelf_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as camera_no',
            'locations.name as location_name'
        )->leftJoin('cameras', 'cameras.id', '=', 'shelf_detection_rules.camera_id')
        ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }

        return $rules;
    }

    public static function saveData($params)
    {
        $camera_id = $params['camera_id'];
        $hour = $params['hour'];
        $mins = $params['mins'];
        $rule_data = json_decode($params['rule_data']);
        ShelfDetectionRule::query()->where('camera_id', $camera_id)->delete();
        foreach ($rule_data as $rule) {
            if (is_array($rule->points)) {
                $new_rule = new ShelfDetectionRule();
                $new_rule->color = $rule->color;
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
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.contract_no',
                'cameras.camera_id as camera_no',
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
        if (Auth::guard('admin')->user()->contract_no != null) {
            $query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $query->orderByDesc('shelf_detections.starttime');

        return $query;
    }
}
