<?php

namespace App\Service;

use App\Models\LocationDrawing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LocationDrawingService
{
    public static function doCreate($params)
    {
        $new_Drawing = new LocationDrawing();
        $new_Drawing->location_id = $params['location_id'];
        $new_Drawing->floor_number = $params['floor_number'];

        if ($params->input('drawing_file_path')) {
            $file_path = $params->input('drawing_file_path');
            if (Storage::disk('temp')->has($file_path)) {
                $drawing_file = Storage::disk('temp')->get($file_path);
                Storage::disk('drawings')->put($file_path, $drawing_file);
                Storage::disk('temp')->delete($file_path);

                $new_Drawing->drawing_file_path = $file_path;
                $new_Drawing->drawing_file_name = $params->input('drawing_file_name');
            }
        }

        $new_Drawing->created_by = Auth::guard('admin')->user()->id;
        $new_Drawing->updated_by = Auth::guard('admin')->user()->id;

        return $new_Drawing->save();
    }

    public static function doUpdate($params, $cur_Drawing)
    {
        if (is_object($cur_Drawing)) {
            $cur_Drawing->location_id = $params['location_id'];
            $cur_Drawing->floor_number = $params['floor_number'];

            if ($params->input('drawing_file_path')) {
                $file_path = $params->input('drawing_file_path');
                if (Storage::disk('temp')->has($file_path)) {
                    $drawing_file = Storage::disk('temp')->get($file_path);
                    Storage::disk('drawings')->put($file_path, $drawing_file);
                    Storage::disk('temp')->delete($file_path);

                    Storage::disk('drawings')->delete($cur_Drawing->drawing_file_path);
                    $cur_Drawing->drawing_file_path = $file_path;
                    $cur_Drawing->drawing_file_name = $params->input('drawing_file_name');
                }
            }

            $cur_Drawing->updated_by = Auth::guard('admin')->user()->id;

            return $cur_Drawing->save();
        } else {
            return redirect()->route('admin.top');
        }
    }

    public static function doDelete($cur_Drawing)
    {
        if (is_object($cur_Drawing)) {
            return $cur_Drawing->delete();
        } else {
            return redirect()->route('admin.top');
        }
    }

    public static function doSearch($params)
    {
        $drawings = LocationDrawing::query();
        if ($params->has('location') && $params->location > 0) {
            $drawings = $drawings->where('location_id', $params->location);
        }
        if (Auth::guard('admin')->user()->contract_no != null) {
            $drawings->select('location_drawings.*')->leftJoin('locations', 'location_id', 'locations.id')
                ->where('locations.contract_no', Auth::guard('admin')->user()->contract_no)
                ->whereNull('locations.deleted_at');
        }

        return $drawings->with('location');
    }

    public static function getDrawingDataObject($locations)
    {
        $res = [];
        if ($locations == null || count($locations) == 0) {
            return $res;
        }
        $location_ids = array_keys($locations);
        $drawings = LocationDrawing::query()->whereIn('location_id', $location_ids)->get()->all();
        foreach ($drawings as $drawing_item) {
            if (!isset($res[$drawing_item->location_id])) {
                $res[$drawing_item->location_id] = [];
            }
            $res[$drawing_item->location_id][$drawing_item->id] = $drawing_item;
        }

        return $res;
    }

    public static function getDataByLocation($location_id)
    {
        $drawings = LocationDrawing::query()->where('location_id', $location_id);

        return $drawings->get();
    }
}
