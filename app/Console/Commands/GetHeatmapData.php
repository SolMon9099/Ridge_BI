<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Camera;
use App\Models\Heatmap;
use App\Models\S3VideoHistory;
use Illuminate\Support\Facades\DB;

class GetHeatmapData extends Command
{
    protected $signature = 'ai:get_heatmap_data';

    protected $description = 'ヒートマップを計算する用API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('ヒートマップ計算開始');
        $cameras = Camera::all();
        if (count($cameras) == 0) {
            Log::info('登録したカメラがありません。');
            Log::info('ヒートマップ計算終了');

            return 0;
        }
        foreach ($cameras as $camera) {
            if ($camera->contract_no == null || $camera->is_enabled == 0) {
                continue;
            }
            //ヒートマップDBチェック----------------
            $heat_map_data = Heatmap::query()->where('camera_id', $camera->camera_id)->orderby('starttime')->get()->all();
            if (count($heat_map_data) >= config('const.heatmap_video_min_numbers')) {
                Log::info('ヒートマップデータが登録されているカメラ：　'.$camera->camera_id);
                continue;
            }
            $start_datetime = null;
            $registered_starttime_array = [];
            if (count($heat_map_data) > 0) {
                $start_datetime = $heat_map_data[0]->starttime;
                foreach ($heat_map_data as $heat_item) {
                    $registered_starttime_array[] = $heat_item->starttime;
                }
            } else {
                $deleted_heat_data = DB::table('heatmaps')->where('camera_id', $camera->camera_id)->whereNotNull('deleted_at')->orderByDesc('deleted_at')->get()->first();
                if ($deleted_heat_data != null) {
                    $start_datetime = $deleted_heat_data->deleted_at;
                }
            }

            //------------------------------------
            //s3の保存された動画チェック-----------
            $s3_data_query = S3VideoHistory::query()->where('device_id', $camera->camera_id)->whereNotNull('file_path')->orderByDesc('start_time');
            if ($start_datetime != null) {
                $s3_data_query->where('start_time', '>', $start_datetime);
            }
            if (count($registered_starttime_array) > 0) {
                $s3_data_query->whereNotIn('start_time', $registered_starttime_array);
            }
            $s3_item = $s3_data_query->get()->first();
            if ($s3_item == null) {
                Log::info('登録した動画がありません。：　'.$camera->camera_id);
                continue;
            }
            //-----------------------------------
            $movie_path_array = [];
            // foreach ($s3_data as $s3_item) {
            //     $movie_path_array[] = config('const.aws_url').$s3_item->file_path;
            // }
            $params = [
                'camera_info' => ['camera_id' => $camera->camera_id],
                'movie_info' => [
                    'movie_path' => config('const.aws_url').$s3_item->file_path,
                ],
                'model_info' => [
                    'grid_size' => [128, 72],
                    'detection_threshold' => 0.2,
                    'read_frequency' => 0.1,
                ],
                'priority' => 1,
                // 'movie_path_array' => $movie_path_array,
            ];
            $header = [
                'Content-Type: application/json',
            ];
            Log::info('ヒートマップ計算リクエスト（BI→AI）開始ーーーー');
            $url = config('const.ai_server').'heatmap/register-camera';
            if ($camera->camera_id == 'FvY6rnGWP12obPgFUj0a') {
                $url = 'http://3.114.15.58/api/v1/'.'heatmap/register-camera';
            }
            $res = $this->sendPostApi($url, $header, $params, 'json');
            if ($res != null) {
                Log::info('ヒートマップ計算リクエストをDBに保存');
                $new_heatmap = new Heatmap();
                $new_heatmap->camera_id = $camera->camera_id;
                $new_heatmap->quality_score = '';
                $new_heatmap->heatmap_data = '';
                $new_heatmap->starttime = $s3_item->start_time;
                $new_heatmap->endtime = $s3_item->end_time;
                $new_heatmap->time_diff = strtotime($s3_item->end_time) - strtotime($s3_item->start_time);
                $new_heatmap->status = 1;
                $new_heatmap->save();
            }
        }
        Log::info('ヒートマップ計算終了');

        return 0;
    }

    public function sendPostApi($url, $header = null, $data = null, $request_type = 'query')
    {
        Log::info('【Start Post Api for AI】url:'.$url);

        $curl = curl_init($url);
        //POSTで送信
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($data) {
            switch ($request_type) {
                case 'query':
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    break;
                case 'json':
                    Log::info('post param data ='.json_encode($data));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
            }
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Log::info('httpcode = '.$httpcode);
        curl_close($curl);
        if ($httpcode == 200) {
            $response_return = json_decode($response, true);

            Log::info('【Finish Post Api】url:'.$url);

            return $httpcode;
        } else {
            return null;
        }
    }
}
