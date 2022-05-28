<?php

namespace App\Service;

use App\Models\Camera;
use Auth;

class CameraService
{
    public static function doSearch($params)
    {
        $cameras = Camera::query();
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
        $new_Camera = new Camera();
        $new_Camera->camera_id = $params['camera_id'];
        if ($params['location_id'] == 0) {
            $new_Camera->location_id = null;
        } else {
            $new_Camera->location_id = $params['location_id'];
        }
        $new_Camera->installation_floor = $params['installation_floor'];
        $new_Camera->installation_position = $params['installation_position'];
        $new_Camera->remarks = $params['remarks'];
        $new_Camera->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;

        $new_Camera->created_by = Auth::guard('admin')->user()->id;
        $new_Camera->updated_by = Auth::guard('admin')->user()->id;

        return $new_Camera->save();
    }

    public static function doUpdate($params, $cur_Camera)
    {
        if (is_object($cur_Camera)) {
            $cur_Camera->camera_id = $params['camera_id'];
            if ($params['location_id'] == 0) {
                $cur_Camera->location_id = null;
            } else {
                $cur_Camera->location_id = $params['location_id'];
            }
            $cur_Camera->installation_floor = $params['installation_floor'];
            $cur_Camera->installation_position = $params['installation_position'];
            $cur_Camera->remarks = $params['remarks'];
            $cur_Camera->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;
            $cur_Camera->updated_by = Auth::guard('admin')->user()->id;

            return $cur_Camera->save();
        } else {
            abort(403);
        }
    }

    public static function doDelete($cur_Camera)
    {
        if (is_object($cur_Camera)) {
            return $cur_Camera->delete();
        } else {
            abort(403);
        }
    }

    public static function getCameraInfoById($id)
    {
        return Camera::find($id);
    }
}
