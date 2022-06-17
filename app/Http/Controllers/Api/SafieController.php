<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\SafieApiService;

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
}
