<?php

namespace App\Service;

use App\Models\Camera;
use App\Models\DangerAreaDetectionRule;
use App\Models\DangerAreaDetection;
use Illuminate\Support\Facades\Auth;

class DangerService
{
    public static function doSearch($params)
    {
        $danger_rules = DangerAreaDetectionRule::select(
            'danger_area_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as camera_no',
            'locations.name as location_name'
        )->leftJoin('cameras', 'cameras.id', '=', 'danger_area_detection_rules.camera_id')
        ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $danger_rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }

        return $danger_rules;
    }

    public static function saveData($params)
    {
        $color = $params['color'];
        $actions = $params['actions'];
        $camera_id = $params['camera_id'];
        $points = $params['points_data'];
        foreach (config('const.action_code') as $action_id) {
            $cur_danger = DangerAreaDetectionRule::query()->where('action_id', $action_id)->where('camera_id', $camera_id)->get()->first();
            if (in_array($action_id, $actions)) {
                if (isset($cur_danger)) {
                    $cur_danger->action_id = $action_id;
                    $cur_danger->color = $color;
                    $cur_danger->points = $points;
                    $cur_danger->updated_by = Auth::guard('admin')->user()->id;

                    $res = $cur_danger->save();
                    if (!$res) {
                        return false;
                    }
                } else {
                    $new_Danger = new DangerAreaDetectionRule();
                    $new_Danger->action_id = $action_id;
                    $new_Danger->color = $color;
                    $new_Danger->camera_id = $camera_id;
                    $new_Danger->points = $points;

                    $new_Danger->created_by = Auth::guard('admin')->user()->id;
                    $new_Danger->updated_by = Auth::guard('admin')->user()->id;

                    $res = $new_Danger->save();
                    if (!$res) {
                        return false;
                    }
                }
            } else {
                if (isset($cur_danger)) {
                    $cur_danger->delete();
                }
            }
        }

        return true;
    }

    public static function doDelete($cur_Danger)
    {
        if (is_object($cur_Danger)) {
            return $cur_Danger->delete();
        } else {
            abort(403);
        }
    }

    public static function getDangerInfoById($id)
    {
        return DangerAreaDetectionRule::query()->where('id', $id)->get();
    }

    public static function getRulesByCameraID($camera_id)
    {
        return DangerAreaDetectionRule::query()->where('camera_id', $camera_id)->get();
    }

    public static function getCameraByRuleID($rule_id)
    {
        $res = null;
        $rule_data = self::getDangerInfoById($rule_id)->first();
        if (isset($rule_data)) {
            $camera_id = $rule_data->camera_id;
            $res = CameraService::getCameraInfoById($camera_id);
        }

        return $res;
    }

    public static function searchDetections($params)
    {
        $query = DangerAreaDetection::query()
            ->select(
                'danger_area_detections.*',
                'danger_area_detection_rules.action_id',
                'danger_area_detection_rules.color',
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.contract_no',
                'locations.name as location_name'
            )
            ->leftJoin('danger_area_detection_rules', 'danger_area_detection_rules.id', 'danger_area_detections.rule_id')
            ->leftJoin('cameras', 'cameras.id', 'danger_area_detections.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (isset($params['starttime']) && $params['starttime'] != '') {
            $query->whereDate('danger_area_detections.starttime', '>=', $params['starttime']);
        } else {
            $query->whereDate('danger_area_detections.starttime', '>=', date('Y-m-01'));
        }
        if (isset($params['endtime']) && $params['endtime'] != '') {
            $query->whereDate('danger_area_detections.starttime', '<=', $params['endtime']);
        } else {
            $query->whereDate('danger_area_detections.starttime', '<=', date('Y-m-t'));
        }
        if (isset($params['rule_ids']) && $params['rule_ids'] != '') {
            $rule_ids = json_decode($params['rule_ids']);
            if (count($rule_ids) > 0) {
                $query->whereIn('danger_area_detections.rule_id', $rule_ids);
            }
        }
        if (isset($params['selected_rules']) && is_array($params['selected_rules']) && count($params['selected_rules']) > 0) {
            $query->whereIn('danger_area_detections.rule_id', $params['selected_rules']);
        }
        if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
            $query->whereIn('danger_area_detections.camera_id', $params['selected_cameras']);
        }
        if (isset($params['selected_actions']) && is_array($params['selected_actions']) && count($params['selected_actions']) > 0) {
            $query->whereIn('danger_area_detection_rules.action_id', $params['selected_actions']);
        }
        if (Auth::guard('admin')->user()->contract_no != null) {
            $query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }

        return $query;
    }

    public static function getAllCameras()
    {
        $camera_ids = DangerAreaDetectionRule::query()->distinct('camera_id')->pluck('camera_id');
        $camera_query = Camera::query()->whereIn('cameras.id', $camera_ids)->select('cameras.*', 'locations.name as location_name');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $camera_query->leftJoin('locations', 'locations.id', 'cameras.location_id');

        return $camera_query->get()->all();
    }
}
