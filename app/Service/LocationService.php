<?php

namespace App\Service;

use App\Models\Location;
use Auth;

class LocationService
{
    private function get_arrays($arr) 
    {
        $ret = array();
        foreach($arr as $key => $item) {
            if ($item && $item != "0") $ret[$key] = $item;
        }
        return $ret;
    }

    public static function doCreate($params)
    {
        $new_Location = new Location();
        $new_Location->code = $params['code'];
        $new_Location->name = $params['name'];
        $new_Location->owner = implode(",", self::get_arrays($params['owners']));
        $new_Location->manager = implode(",", self::get_arrays($params['managers']));
        $new_Location->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;

        $new_Location->created_by = Auth::guard('admin')->user()->id;
        $new_Location->updated_by = Auth::guard('admin')->user()->id;

        return $new_Location->save();
    }

    public static function doUpdate($params, $cur_Location)
    {
        if (is_object($cur_Location)) {
            $cur_Location->code = $params['code'];
            $cur_Location->name = $params['name'];
            $cur_Location->owner = implode(",", self::get_arrays($params['owners']));
            $cur_Location->manager = implode(",", self::get_arrays($params['managers']));
            $cur_Location->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;
            $cur_Location->updated_by = Auth::guard('admin')->user()->id;

            return $cur_Location->save();
        } else {
            abort(403);
        }
    }

    public static function doDelete($cur_Location)
    {
        if (is_object($cur_Location)) {
            return $cur_Location->delete();
        } else {
            abort(403);
        }
    }

    public static function getLocationInfoById($id)
    {
        return Location::find($id);
    }

    public static function getAllLocationNames()
    {
        $locations = Location::orderBy('id', 'asc')->get();
        $locations_array = [];
        foreach ($locations as $location) {
            $locations_array[$location->id] = $location->name;
        }

        return $locations_array;
    }
}
