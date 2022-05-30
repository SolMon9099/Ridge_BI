<?php

namespace App\Service;

use App\Models\DangerAreaDetectionRule;
use Auth;

class DangerService
{
    public static function doSearch($params)
    {
        $cameras = DangerAreaDetectionRule::query();
        if ($params->has('location') && $params->location > 0) {
            $cameras = $cameras->where('location_id', $params->location);
        }
        if ($params->has('installation_floor') && $params->installation_floor != '') {
            $cameras = $cameras->where('installation_floor', 'LIKE', '%'.$params->installation_floor.'%');
        }
        if ($params->has('is_enabled')) {
            $cameras = $cameras->where('is_enabled', $params->is_enabled ? 1 : 0);
        }

        return $cameras;
    }

    public static function doCreate($params)
    {
        $new_Danger = new DangerAreaDetectionRule();
        $new_Danger->camera_id = $params['camera_id'];
        if ($params['location_id'] == 0) {
            $new_Danger->location_id = null;
        } else {
            $new_Danger->location_id = $params['location_id'];
        }
        $new_Danger->installation_floor = $params['installation_floor'];
        $new_Danger->installation_position = $params['installation_position'];
        $new_Danger->remarks = $params['remarks'];
        $new_Danger->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;

        $new_Danger->created_by = Auth::guard('admin')->user()->id;
        $new_Danger->updated_by = Auth::guard('admin')->user()->id;

        return $new_Danger->save();
    }

    public static function doUpdate($params, $cur_Danger)
    {
        if (is_object($cur_Danger)) {
            $cur_Danger->camera_id = $params['camera_id'];
            if ($params['location_id'] == 0) {
                $cur_Danger->location_id = null;
            } else {
                $cur_Danger->location_id = $params['location_id'];
            }
            $cur_Danger->installation_floor = $params['installation_floor'];
            $cur_Danger->installation_position = $params['installation_position'];
            $cur_Danger->remarks = $params['remarks'];
            $cur_Danger->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;
            $cur_Danger->updated_by = Auth::guard('admin')->user()->id;

            return $cur_Danger->save();
        } else {
            abort(403);
        }
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
        return DangerAreaDetectionRule::find($id);
    }
}
