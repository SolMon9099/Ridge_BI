<?php

namespace App\Service;

use App\Models\Camera;
use App\Models\PitDetectionRule;
use App\Models\PitDetection;
use Illuminate\Support\Facades\Auth;

class PitService
{
    public static function doSearch($params)
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

        return $pit_rules;
    }

    public static function saveData($params)
    {
        $red_points_data = $params['red_points_data'];
        $blue_points_data = $params['blue_points_data'];
        $camera_id = $params['camera_id'];
        $cur_pit = self::getRulesByCameraID($camera_id)->first();
        if ($cur_pit != null) {
            $cur_pit->red_points = $red_points_data;
            $cur_pit->blue_points = $blue_points_data;
            if (isset($params['max_permission_time']) && $params['max_permission_time'] > 0) {
                $cur_pit->max_permission_time = $params['max_permission_time'];
            } else {
                $cur_pit->max_permission_time = null;
            }
            $cur_pit->updated_by = Auth::guard('admin')->user()->id;
            $res = $cur_pit->save();
            if (!$res) {
                return false;
            }
        } else {
            $new_pit = new PitDetectionRule();
            $new_pit->camera_id = $camera_id;
            $new_pit->red_points = $red_points_data;
            $new_pit->blue_points = $blue_points_data;
            if (isset($params['max_permission_time']) && $params['max_permission_time'] > 0) {
                $new_pit->max_permission_time = $params['max_permission_time'];
            }
            $new_pit->created_by = Auth::guard('admin')->user()->id;
            $new_pit->updated_by = Auth::guard('admin')->user()->id;

            $res = $new_pit->save();
            if (!$res) {
                return false;
            }
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
                'locations.name as location_name',
                'pit_detection_rules.max_permission_time'
            )
            ->leftJoin('pit_detection_rules', 'pit_detection_rules.id', 'pit_detections.rule_id')
            ->leftJoin('cameras', 'cameras.id', 'pit_detections.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if ($today_flag == true) {
            $query->whereDate('pit_detections.starttime', date('Y-m-d'));
        } else {
            if (isset($params['searchdate']) && $params['searchdate'] != '') {
                $query->whereDate('pit_detections.starttime', $params['searchdate']);
            } else {
                $query->whereDate('pit_detections.starttime', date('Y-m-d'));
            }
        }

        if (isset($params['rule_ids']) && $params['rule_ids'] != '') {
            $rule_ids = json_decode($params['rule_ids']);
            if (count($rule_ids) > 0) {
                $query->whereIn('pit_detections.rule_id', $rule_ids);
            }
        }
        if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
            $query->whereIn('pit_detections.camera_id', $params['selected_cameras']);
        }
        if (Auth::guard('admin')->user()->contract_no != null) {
            $query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }

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
}
