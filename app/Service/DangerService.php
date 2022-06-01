<?php

namespace App\Service;

use App\Models\DangerAreaDetectionRule;
use Auth;

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
        if (isset($params['id']) && $params['id'] > 0) {
            $cur_danger = DangerAreaDetectionRule::find($params['id']);
            $cur_danger->action_id = $params['action_id'];
            $cur_danger->color = $params['color'];
            foreach ($params['points'] as $key => $point) {
                switch ($key) {
                    case 0:
                        $cur_danger->first_x = $point->x;
                        $cur_danger->first_y = $point->y;
                        break;
                    case 1:
                        $cur_danger->second_x = $point->x;
                        $cur_danger->second_y = $point->y;
                        break;
                    case 2:
                        $cur_danger->third_x = $point->x;
                        $cur_danger->third_y = $point->y;
                        break;
                    case 3:
                        $cur_danger->fourth_x = $point->x;
                        $cur_danger->fourth_y = $point->y;
                        break;
                }
            }

            return $cur_danger->save();
        } else {
            $new_Danger = new DangerAreaDetectionRule();
            $new_Danger->action_id = $params['action_id'];
            $new_Danger->color = $params['color'];
            $new_Danger->camera_id = $params['camera_id'];
            foreach ($params['points'] as $key => $point) {
                switch ($key) {
                    case 0:
                        $new_Danger->first_x = $point->x;
                        $new_Danger->first_y = $point->y;
                        break;
                    case 1:
                        $new_Danger->second_x = $point->x;
                        $new_Danger->second_y = $point->y;
                        break;
                    case 2:
                        $new_Danger->third_x = $point->x;
                        $new_Danger->third_y = $point->y;
                        break;
                    case 3:
                        $new_Danger->fourth_x = $point->x;
                        $new_Danger->fourth_y = $point->y;
                        break;
                }
            }

            $new_Danger->created_by = Auth::guard('admin')->user()->id;
            $new_Danger->updated_by = Auth::guard('admin')->user()->id;

            return $new_Danger->save();
        }
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

    public static function getRulesByCameraID($camera_id)
    {
        return DangerAreaDetectionRule::query()->where('camera_id', $camera_id)->get();
    }
}
