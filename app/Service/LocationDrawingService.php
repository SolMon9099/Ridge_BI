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
            abort(403);
        }
    }

    public static function doDelete($cur_Drawing)
    {
        if (is_object($cur_Drawing)) {
            return $cur_Drawing->delete();
        } else {
            abort(403);
        }
    }

    public static function doSearch($params)
    {
        $drawings = LocationDrawing::query();
        if ($params->has('location') && $params->location > 0) {
            $drawings = $drawings->where('location_id', $params->location);
        }

        return $drawings->with('location');
    }

    public static function getDataByLocation($location_id)
    {
        $drawings = LocationDrawing::query()->where('location_id', $location_id);

        return $drawings->get();
    }
}
