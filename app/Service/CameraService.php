<?php

namespace App\Service;

use App\Models\Camera;
use App\Models\CameraMappingDetail;
use Illuminate\Support\Facades\Auth;

class CameraService
{
    public static function doSearch($params)
    {
        $cameras = Camera::query();
        if ($params->has('location') && $params->location > 0) {
            $cameras = $cameras->where('cameras.location_id', $params->location);
        }
        if ($params->has('floor_number') && $params->floor_number != '') {
            $cameras->select('cameras.*', 'drawing.floor_number')->leftJoin('camera_mapping_details as map', 'cameras.id', 'map.camera_id')->whereNull('map.deleted_at')
                ->leftJoin('location_drawings as drawing', 'drawing.id', 'map.drawing_id')->whereNull('drawing.deleted_at')
                ->where('drawing.floor_number', 'LIKE', '%'.$params->floor_number.'%');
        }
        if ($params->has('is_enabled')) {
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

    public static function getCameraByLocation($location_id)
    {
        $camera_query = Camera::query()->where('location_id', $location_id);
        if (Auth::guard('admin')->user()->contract_no != null) {
            $camera_query->where('contract_no', Auth::guard('admin')->user()->contract_no);
        }

        return $camera_query->get();
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

    public static function getAllCameraNames()
    {
        $cameras = Camera::orderBy('id', 'asc')->get();
        $cameras_array = [];
        foreach ($cameras as $camera) {
            $cameras_array[$camera->id] = $camera->camera_id;
        }

        return $cameras_array;
    }
}
