<?php

namespace App\Service;

use App\Models\DangerAreaDetectionRule;
use Illuminate\Support\Facades\Auth;

class DangerService
{
    public static function doSearch($params)
    {
        $danger_rules = DangerAreaDetectionRule::select(
            'danger_area_detection_rules.*',
            'cameras.installation_floor',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as camera_no'
        )->leftJoin('cameras', 'cameras.id', '=', 'danger_area_detection_rules.camera_id');

        return $danger_rules;
    }

    public static function saveData($params)
    {
        $params = (array) $params;
        foreach ($params as $param) {
            $param = (array) $param;
            if (isset($param['id']) && $param['id'] > 0) {
                $cur_danger = DangerAreaDetectionRule::find($param['id']);
                if (isset($param['is_deleted']) && $param['is_deleted'] == true) {
                    $res = $cur_danger->delete();
                } else {
                    $cur_danger->action_id = $param['action_id'];
                    $cur_danger->color = $param['color'];
                    $cur_danger->points = isset($param['points']) && $param['points'] != '' ? json_encode($param['points']) : '';
                    $cur_danger->updated_by = Auth::guard('admin')->user()->id;

                    $res = $cur_danger->save();
                }
                if (!$res) {
                    return false;
                }
            } else {
                if (isset($param['is_deleted']) && $param['is_deleted'] == true) {
                    continue;
                }
                $new_Danger = new DangerAreaDetectionRule();
                $new_Danger->action_id = $param['action_id'];
                $new_Danger->color = $param['color'];
                $new_Danger->camera_id = $param['camera_id'];
                $new_Danger->points = isset($param['points']) && $param['points'] != '' ? json_encode($param['points']) : '';

                $new_Danger->created_by = Auth::guard('admin')->user()->id;
                $new_Danger->updated_by = Auth::guard('admin')->user()->id;

                $res = $new_Danger->save();
                if (!$res) {
                    return false;
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
}
