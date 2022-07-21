<?php

namespace App\Service;

use App\Models\ThiefDetectionRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThiefService
{
    public static function doSearch()
    {
        $rules = ThiefDetectionRule::select(
            'thief_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as camera_no'
        )->leftJoin('cameras', 'cameras.id', '=', 'thief_detection_rules.camera_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }

        return $rules;
    }

    public static function saveData($params)
    {
        $camera_id = $params['camera_id'];
        $points_data = $params['points_data'];
        $points_data = json_decode($points_data);
        $hanger = $params['hanger'];
        DB::table('thief_detection_rules')->where('camera_id', $camera_id)->delete();
        foreach ($points_data as $point) {
            if (is_array($point->positions) && count($point->positions) == 4) {
                $new_rule = new ThiefDetectionRule();
                $new_rule->color = $point->color;
                $new_rule->camera_id = $camera_id;
                $new_rule->points = json_encode($point->positions);
                $new_rule->hanger = $hanger;
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
}
