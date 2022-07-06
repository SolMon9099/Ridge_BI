<?php

namespace App\Service;

use App\Models\ShelfDetectionRule;
use Illuminate\Support\Facades\Auth;

class ShelfService
{
    public static function doSearch()
    {
        $rules = ShelfDetectionRule::select(
            'shelf_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as camera_no'
        )->leftJoin('cameras', 'cameras.id', '=', 'shelf_detection_rules.camera_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }

        return $rules;
    }

    public static function saveData($params)
    {
        $params = (array) $params;
        foreach ($params as $param) {
            $param = (array) $param;
            if (isset($param['id']) && $param['id'] > 0) {
                $cur_rule = ShelfDetectionRule::find($param['id']);
                if (isset($param['is_deleted']) && $param['is_deleted'] == true) {
                    $res = $cur_rule->delete();
                } else {
                    $cur_rule->color = $param['color'];
                    $cur_rule->points = isset($param['points']) && $param['points'] != '' ? json_encode($param['points']) : '';
                    $cur_rule->updated_by = Auth::guard('admin')->user()->id;
                    $res = $cur_rule->save();
                }
                if (!$res) {
                    return false;
                }
            } else {
                if (isset($param['is_deleted']) && $param['is_deleted'] == true) {
                    continue;
                }
                $new_rule = new ShelfDetectionRule();
                $new_rule->color = $param['color'];
                $new_rule->camera_id = $param['camera_id'];
                $new_rule->points = isset($param['points']) && $param['points'] != '' ? json_encode($param['points']) : '';

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
}
