<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\DB;

class SafieController extends Controller
{
    public function __construct()
    {
    }

    public function getImage(Request $request)
    {
        if (isset($request['device_id']) && $request['device_id'] != '') {
            $safie_service = new SafieApiService();
            $camera_image_data = $safie_service->getDeviceImage();

            return $camera_image_data;
        } else {
            return ['error' => 'There is no device ID'];
        }
    }

    public function getLiveStreaming(Request $request)
    {
        if (isset($request['device_id']) && $request['device_id'] != '') {
            $safie_service = new SafieApiService();

            return $safie_service->getDeviceLiveStreamingList();
        } else {
            return ['error' => 'There is no device ID'];
        }
    }

    public function getReactInfo(Request $request)
    {
        if (isset($request['camera_id']) && $request['camera_id'] != '') {
            $camera_data = DB::table('cameras')->where('camera_id', $request['camera_id'])->whereNull('deleted_at')->get()->first();
            if ($camera_data == null) {
                return ['error' => 'デバイスがありません。'];
            }
            $camera_id = $camera_data->id;
            $res = [];
            $danger_data = DB::table('danger_area_detection_rules')->where('camera_id', $camera_id)->whereNull('deleted_at')->get()->all();
            foreach ($danger_data as $item) {
                $res[] = ['rect_id' => 'danger_'.$item->id, 'rect_point_array' => json_decode($item->points)];
            }
            $pit_data = DB::table('pit_detection_rules')->where('camera_id', $camera_id)->whereNull('deleted_at')->get()->all();
            foreach ($pit_data as $item) {
                $red_points = json_decode($item->red_points);
                $blue_points = json_decode($item->blue_points);
                $res[] = ['rect_id' => 'pit_'.$item->id, 'rect_point_array' => array_merge($red_points, $blue_points)];
            }
            $shelf_data = DB::table('shelf_detection_rules')->where('camera_id', $camera_id)->whereNull('deleted_at')->get()->all();
            foreach ($shelf_data as $item) {
                $res[] = ['rect_id' => 'shelf_'.$item->id, 'rect_point_array' => json_decode($item->points)];
            }
            $thief_data = DB::table('thief_detection_rules')->where('camera_id', $camera_id)->whereNull('deleted_at')->get()->all();
            foreach ($thief_data as $item) {
                $res[] = ['rect_id' => 'thief_'.$item->id, 'rect_point_array' => json_decode($item->points)];
            }

            return $res;
        } else {
            return ['error' => 'デバイスがありません。'];
        }
    }
}
