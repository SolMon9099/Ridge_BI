<?php

namespace App\Service;

use App\Models\Camera;
use App\Models\DangerAreaDetectionRule;
use App\Models\DangerAreaDetection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DangerService
{
    public static function doSearch($params = null)
    {
        $danger_rules = DangerAreaDetectionRule::select(
            'danger_area_detection_rules.*',
            'cameras.installation_position',
            'cameras.location_id',
            'cameras.camera_id as device_id',
            'cameras.serial_no',
            'cameras.contract_no',
            'locations.name as location_name'
        )->leftJoin('cameras', 'cameras.id', '=', 'danger_area_detection_rules.camera_id')
        ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $danger_rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no)->whereNull('cameras.deleted_at');
        }
        if ($params != null) {
            if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')) {
                $danger_rules->where(function ($q) {
                    $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                    $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                    $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
                });
            }
            if (isset($params['selected_cameras']) && !is_array($params['selected_cameras']) && $params['selected_cameras'] != '') {
                $selected_cameras = json_decode($params['selected_cameras']);
                $danger_rules->whereIn('danger_area_detection_rules.camera_id', $selected_cameras);
            }
            if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
                $danger_rules->whereIn('danger_area_detection_rules.camera_id', $params['selected_cameras']);
            }
            if (isset($params['selected_camera']) && $params['selected_camera'] > 0) {
                $danger_rules->where('danger_area_detection_rules.camera_id', $params['selected_camera']);
            }
            if (isset($params) && $params->has('location') && $params->location > 0) {
                $danger_rules->where('cameras.location_id', $params->location);
            }
            if (isset($params) && $params->has('installation_position') && $params->installation_position != '') {
                $danger_rules->where('cameras.installation_position', 'like', '%'.$params->installation_position.'%');
            }
            if (isset($params) && $params->has('floor_number') && $params->floor_number != '') {
                $danger_rules->leftJoin('camera_mapping_details as map', 'cameras.id', 'map.camera_id')->whereNull('map.deleted_at')
                    ->leftJoin('location_drawings as drawing', 'drawing.id', 'map.drawing_id')->whereNull('drawing.deleted_at')
                    ->where('drawing.floor_number', 'LIKE', '%'.$params->floor_number.'%');
            }
        }

        return $danger_rules;
    }

    public static function getAllRules()
    {
        $danger_rules = DB::table('danger_area_detection_rules')
            ->select(
                'danger_area_detection_rules.*',
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.camera_id as device_id',
                'cameras.serial_no',
                'cameras.contract_no',
                'locations.name as location_name'
            )->leftJoin('cameras', 'cameras.id', '=', 'danger_area_detection_rules.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id')
            ->whereIn('danger_area_detection_rules.id', function ($q) {
                $q->select('rule_id')->from('danger_area_detections');
            });
        if (Auth::guard('admin')->user()->contract_no != null) {
            $danger_rules->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')) {
            $danger_rules->where(function ($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
        }

        $danger_rules->orderByRaw('-danger_area_detection_rules.deleted_at', 'DESC')->orderByDesc('danger_area_detection_rules.updated_at');

        return $danger_rules;
    }

    public static function saveData($params)
    {
        $camera_id = $params['camera_id'];
        $rule_data = (array) json_decode($params['rule_data']);
        if (count($rule_data) > 0) {
            // DangerAreaDetectionRule::query()->where('camera_id', $camera_id)->delete();
            $unchanged_ids = [];
            foreach ($rule_data as $rule_item) {
                if (isset($rule_item->id) && $rule_item->id > 0) {
                    $cur_danger = DangerAreaDetectionRule::find($rule_item->id);
                    if ($cur_danger == null) {
                        return '編集中にルールが変更されたため登録出来ませんでした';
                    }
                    if (!(isset($rule_item->is_changed) && $rule_item->is_changed == true)) {
                        $unchanged_ids[] = $rule_item->id;
                        if (isset($rule_item->is_name_color_changed) && $rule_item->is_name_color_changed == true) {
                            $cur_danger->name = isset($rule_item->name) && $rule_item->name != '' ? $rule_item->name : null;
                            $cur_danger->color = $rule_item->color;
                            $cur_danger->save();
                        }
                    }
                }
            }
            DangerAreaDetectionRule::query()->where('camera_id', $camera_id)->whereNotIn('id', $unchanged_ids)->delete();
            foreach ($rule_data as $rule_item) {
                if (isset($rule_item->id) && $rule_item->id > 0 && !(isset($rule_item->is_changed) && $rule_item->is_changed == true)) {
                    continue;
                }
                $new_Danger = new DangerAreaDetectionRule();
                $new_Danger->action_id = json_encode($rule_item->action_id);
                $new_Danger->name = isset($rule_item->name) && $rule_item->name != '' ? $rule_item->name : null;
                $new_Danger->color = $rule_item->color;
                $new_Danger->camera_id = $camera_id;
                $new_Danger->points = json_encode($rule_item->points);
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

    public static function getDangerInfoById($id, $only_enalbe = true)
    {
        if ($only_enalbe) {
            return DangerAreaDetectionRule::query()->where('id', $id)->get();
        } else {
            return DB::table('danger_area_detection_rules')->where('id', $id)->get();
        }
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
                'danger_area_detection_rules.points',
                'danger_area_detection_rules.color',
                'danger_area_detection_rules.name as rule_name',
                'cameras.installation_position',
                'cameras.location_id',
                'cameras.contract_no',
                'cameras.camera_id as device_id',
                'cameras.serial_no',
                'locations.name as location_name'
            )
            ->leftJoin('danger_area_detection_rules', 'danger_area_detection_rules.id', 'danger_area_detections.rule_id')
            ->leftJoin('cameras', 'cameras.id', 'danger_area_detections.camera_id')
            ->leftJoin('locations', 'locations.id', 'cameras.location_id');
        if ($params != null) {
            if (isset($params['starttime']) && $params['starttime'] != '') {
                $query->whereDate('danger_area_detections.starttime', '>=', $params['starttime']);
            } else {
                if ($params != null) {
                    $query->whereDate('danger_area_detections.starttime', '>=', date('Y-m-d', strtotime('-1 week')));
                }
            }
            if (isset($params['endtime']) && $params['endtime'] != '') {
                $query->whereDate('danger_area_detections.starttime', '<=', $params['endtime']);
            } else {
                $query->whereDate('danger_area_detections.starttime', '<=', date('Y-m-d'));
            }
            if (isset($params['rule_ids']) && $params['rule_ids'] != '') {
                $rule_ids = json_decode($params['rule_ids']);
                if (count($rule_ids) > 0) {
                    $query->whereIn('danger_area_detections.rule_id', $rule_ids);
                }
            }
            if (isset($params['selected_rule']) && $params['selected_rule'] > 0) {
                $query->where('danger_area_detections.rule_id', $params['selected_rule']);
            }
            if (isset($params['selected_rules']) && is_array($params['selected_rules']) && count($params['selected_rules']) > 0) {
                $query->whereIn('danger_area_detections.rule_id', $params['selected_rules']);
            }
            if (isset($params['selected_cameras']) && is_array($params['selected_cameras']) && count($params['selected_cameras']) > 0) {
                $query->whereIn('danger_area_detections.camera_id', $params['selected_cameras']);
            }
            if (isset($params['selected_camera']) && $params['selected_camera'] > 0) {
                $query->where('danger_area_detections.camera_id', $params['selected_camera']);
            }
            if (isset($params['selected_actions']) && is_array($params['selected_actions']) && count($params['selected_actions']) > 0) {
                $query->whereIn('danger_area_detection_rules.action_id', $params['selected_actions']);
            }
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
        $query->orderByDesc('danger_area_detections.starttime');

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
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')) {
            $camera_query->where(function ($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
        }

        return $camera_query->get();
    }
}
