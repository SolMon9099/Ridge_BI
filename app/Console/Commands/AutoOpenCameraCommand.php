<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Camera;
use App\Models\MediaRequestHistory;

class AutoOpenCameraCommand extends Command
{
    protected $signature = 'auto_open_camera';
    protected $description = 'カメラ自動動画取得再開チェック';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('カメラ自動動画取得再開チェック開始ーーーーーー');
        $disabled_cameras = Camera::query()->where('is_enabled', 0)->get()->all();
        foreach ($disabled_cameras as $item) {
            $media_history_query = MediaRequestHistory::query()->where('device_id', $item->camera_id)
                ->where('http_code', '!=', 200)
                ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-12 hour')));
            if ($item->reopened_at != null) {
                $media_history_query->where('created_at', '>', date('Y-m-d H:i:s', strtotime($item->reopened_at)));
            }
            $media_history_data = $media_history_query->orderByDesc('created_at')->limit(50)->get()->all();

            if (count($media_history_data) == 50) {
                $check_enable_reopen = true;
                $count_40x_error = 0;
                $diff_from_last = strtotime(date('Y-m-d H:i:s')) - strtotime($media_history_data[0]->created_at);
                if ($diff_from_last >= (int) config('const.camera_auto_reopen_interval')) {
                    foreach ($media_history_data as $history_item) {
                        if ((int) $history_item->http_code != 503) {
                            $check_enable_reopen = false;
                            ++$count_40x_error;
                        }
                    }
                } else {
                    $check_enable_reopen = false;
                }
                if ($count_40x_error <= 5) {
                    $check_enable_reopen = true;
                }
                if ($check_enable_reopen) {
                    Log::info('動画取得再開__ケース①_ '.$item->camera_id);
                    Camera::query()->where('camera_id', $item->camera_id)->update(['is_enabled' => 1]);
                }
            } else {
                if (count($media_history_data) < 5) {
                    $media_history_query = MediaRequestHistory::query()->where('device_id', $item->camera_id);
                    if ($item->reopened_at != null) {
                        $media_history_query->where('created_at', '>', date('Y-m-d H:i:s', strtotime($item->reopened_at)));
                    }
                    $media_history_data = $media_history_query->orderByDesc('created_at')->limit(5)->get()->all();
                }
                $count_40x = 0;
                if (count($media_history_data) < 5) {
                    $count_40x = 0;
                } else {
                    foreach ($media_history_data as $history_item) {
                        if ((int) $history_item->http_code == 200) {
                            $count_40x = 0;
                            break;
                        }
                        if ((int) $history_item->http_code >= 400 && (int) $history_item->http_code <= 500) {
                            ++$count_40x;
                        }
                    }
                }
                if ($count_40x < 4) {
                    Log::info('動画取得再開__ケース_＠__ '.$item->camera_id);
                    Camera::query()->where('camera_id', $item->camera_id)->update(['is_enabled' => 1]);
                }
            }
        }
        Log::info('カメラ自動動画取得再開チェック終了ーーーーーー');

        return 0;
    }
}
