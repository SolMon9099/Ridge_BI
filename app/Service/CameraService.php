<?php

namespace App\Service;

use App\Models\Camera;
use App\Models\CameraMappingDetail;
use App\Models\PitDetectionRule;
use App\Models\DangerAreaDetectionRule;
use App\Models\ShelfDetectionRule;
use App\Models\ThiefDetectionRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CameraService
{
    public static function doSearch($params = null)
    {
        $cameras = Camera::query();
        if (isset($params) && $params->has('location') && $params->location > 0) {
            $cameras = $cameras->where('cameras.location_id', $params->location);
        }
        if (isset($params) && $params->has('floor_number') && $params->floor_number != '') {
            $cameras->select('cameras.*', 'drawing.floor_number')->leftJoin('camera_mapping_details as map', 'cameras.id', 'map.camera_id')->whereNull('map.deleted_at')
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'map.drawing_id')->whereNull('drawing.deleted_at')
                ->where('drawing.floor_number', 'LIKE', '%'.$params->floor_number.'%');
        }
        if (isset($params) && $params->has('is_enabled') && $params->is_enabled != '') {
            $cameras = $cameras->where('is_enabled', $params->is_enabled ? 1 : 0);
        }
        if (Auth::guard('admin')->user()->contract_no != null) {
            $cameras = $cameras->where('contract_no', Auth::guard('admin')->user()->contract_no);
        }

        return $cameras;
    }

    public static function doCreate($params)
    {
        $new_Camera = new Camera();
        $new_Camera->camera_id = $params['camera_id'];
        $new_Camera->serial_no = isset($params['serial_no'])?$params['serial_no']:'';
        if ($params['location_id'] == 0) {
            $new_Camera->location_id = null;
        } else {
            $new_Camera->location_id = $params['location_id'];
        }
        if (Auth::guard('admin')->user()->contract_no != null) {
            $new_Camera->contract_no = Auth::guard('admin')->user()->contract_no;
        }
        $new_Camera->installation_position = $params['installation_position'];
        $new_Camera->remarks = $params['remarks'];
        $new_Camera->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;

        $new_Camera->created_by = Auth::guard('admin')->user()->id;
        $new_Camera->updated_by = Auth::guard('admin')->user()->id;

        $new_Camera->save();
        if ($new_Camera->id > 0 && isset($params['drawing_id']) && $params['drawing_id'] > 0) {
            if (isset($params['x_coordinate']) && $params['y_coordinate'] != null && (int) $params['x_coordinate'] >= 0
                && isset($params['y_coordinate']) && $params['y_coordinate'] != null && (int) $params['y_coordinate'] >= 0) {
                $new_mapping = new CameraMappingDetail();
                $new_mapping->drawing_id = $params['drawing_id'];
                $new_mapping->camera_id = $new_Camera->id;
                $new_mapping->x_coordinate = $params['x_coordinate'];
                $new_mapping->y_coordinate = $params['y_coordinate'];
                $new_mapping->created_by = Auth::guard('admin')->user()->id;
                $new_mapping->updated_by = Auth::guard('admin')->user()->id;

                $new_mapping->save();
            }
        }

        return true;
    }

    public static function doUpdate($params, $cur_Camera)
    {
        if (is_object($cur_Camera)) {
            if ($params['location_id'] == 0) {
                $cur_Camera->location_id = null;
            } else {
                $cur_Camera->location_id = $params['location_id'];
            }
            $cur_Camera->installation_position = $params['installation_position'];
            $cur_Camera->remarks = $params['remarks'];
            $cur_Camera->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;
            $cur_Camera->updated_by = Auth::guard('admin')->user()->id;

            $cur_Camera->save();

            if (isset($params['drawing_id']) && $params['drawing_id'] > 0) {
                $mapping_record = CameraMappingDetail::query()->where('camera_id', $cur_Camera->id)->where('drawing_id', $params['drawing_id'])->get()->first();
                if ($mapping_record != null) {
                    if (isset($params['x_coordinate']) && $params['y_coordinate'] != null && (int) $params['x_coordinate'] >= 0
                        && isset($params['y_coordinate']) && $params['y_coordinate'] != null && (int) $params['y_coordinate'] >= 0) {
                        $mapping_record->x_coordinate = $params['x_coordinate'];
                        $mapping_record->y_coordinate = $params['y_coordinate'];
                        $mapping_record->updated_by = Auth::guard('admin')->user()->id;
                        $mapping_record->save();
                    } else {
                        $mapping_record->delete();
                    }
                } else {
                    if (isset($params['x_coordinate']) && $params['y_coordinate'] != null && (int) $params['x_coordinate'] >= 0
                        && isset($params['y_coordinate']) && $params['y_coordinate'] != null && (int) $params['y_coordinate'] >= 0) {
                        DB::table('camera_mapping_details')->where('camera_id', $cur_Camera->id)->delete();

                        $new_mapping = new CameraMappingDetail();
                        $new_mapping->drawing_id = $params['drawing_id'];
                        $new_mapping->camera_id = $cur_Camera->id;
                        $new_mapping->x_coordinate = $params['x_coordinate'];
                        $new_mapping->y_coordinate = $params['y_coordinate'];
                        $new_mapping->created_by = Auth::guard('admin')->user()->id;
                        $new_mapping->updated_by = Auth::guard('admin')->user()->id;

                        $new_mapping->save();
                    }
                }
            }

            return true;
        } else {
            abort(403);
        }
    }

    public static function doDelete($cur_Camera)
    {
        if (is_object($cur_Camera)) {
            $cur_Camera->delete();
            CameraMappingDetail::query()->where('camera_id', $cur_Camera->id)->delete();
            PitDetectionRule::query()->where('camera_id', $cur_Camera->id)->delete();
            DangerAreaDetectionRule::query()->where('camera_id', $cur_Camera->id)->delete();
            ShelfDetectionRule::query()->where('camera_id', $cur_Camera->id)->delete();
            ThiefDetectionRule::query()->where('camera_id', $cur_Camera->id)->delete();

            return true;
        } else {
            abort(403);
        }
    }

    public static function getCameraInfoById($id)
    {
        return Camera::find($id);
    }

    public static function getCameraByLocation($location_id)
    {
        $camera_query = Camera::query()->where('location_id', $location_id);
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('contract_no', Auth::guard('admin')->user()->contract_no);
        }

        return $camera_query->get();
    }

    public static function getCamerasForRules(){
        $camera_query = Camera::query()->select('cameras.*');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('cameras.contract_no', Auth::guard('admin')->user()->contract_no);
        }
        if (Auth::guard('admin')->user()->authority_id == config('const.authorities_codes.manager')){
            $camera_query->leftJoin('locations', 'locations.id', 'cameras.location_id');
            $camera_query -> where(function($q) {
                $q->orWhere('locations.manager', Auth::guard('admin')->user()->id);
                $q->orWhere('locations.manager', 'Like', '%'.Auth::guard('admin')->user()->id.',%');
                $q->orWhere('locations.manager', 'Like', '%,'.Auth::guard('admin')->user()->id.'%');
            });
            $camera_query->whereNull('locations.deleted_at');
        }
        return $camera_query->orderBy('cameras.id', 'asc');
    }

    public static function storeMapping($camera_mapping_info)
    {
        if (is_object($camera_mapping_info)) {
            foreach ($camera_mapping_info as $drawing_id => $mapping_data) {
                foreach ($mapping_data as $item) {
                    if (isset($item->id) && $item->id > 0) {
                        $cur_record = CameraMappingDetail::find($item->id);
                        if (isset($item->is_deleted) && $item->is_deleted == true) {
                            $cur_record->delete();
                        } else {
                            $cur_record->drawing_id = $item->drawing_id;
                            $cur_record->camera_id = $item->camera_id;
                            $cur_record->x_coordinate = $item->x_coordinate;
                            $cur_record->y_coordinate = $item->y_coordinate;
                            $cur_record->updated_by = Auth::guard('admin')->user()->id;

                            $cur_record->save();
                        }
                    } else {
                        $new_mapping = new CameraMappingDetail();
                        $new_mapping->drawing_id = $item->drawing_id;
                        $new_mapping->camera_id = $item->camera_id;
                        $new_mapping->x_coordinate = $item->x_coordinate;
                        $new_mapping->y_coordinate = $item->y_coordinate;
                        $new_mapping->created_by = Auth::guard('admin')->user()->id;
                        $new_mapping->updated_by = Auth::guard('admin')->user()->id;

                        $new_mapping->save();
                    }
                }
            }

            return true;
        } else {
            abort(403);
        }
    }

    public static function refreshImg()
    {
        $cameras = Camera::orderBy('contract_no');
        if (Auth::guard('admin')->user()->contract_no != null) {
            $cameras = $cameras->where('contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $cameras = $cameras->get()->all();
        $camera_imgs = [];
        $contracts = [];
        foreach ($cameras as $camera) {
            if (!in_array($camera->contract_no, $contracts)) {
                $safie_service = new SafieApiService($camera->contract_no);
                $contracts[] = $camera->contract_no;
            }
            if (!isset($camera_imgs[$camera->camera_id])) {
                $camera_image_data = $safie_service->getDeviceImage($camera->camera_id);
                $camera_imgs[$camera->camera_id] = $camera_image_data;
                Storage::disk('recent_camera_image')->put($camera->camera_id.'.jpeg', $camera_image_data);
            }
        }

        return true;
    }
}
