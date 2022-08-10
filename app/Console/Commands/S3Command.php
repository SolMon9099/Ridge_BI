<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use App\Service\DangerService;
use App\Service\PitService;
use App\Service\ShelfService;
use App\Service\ThiefService;
use App\Models\S3VideoHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Camera;

class S3Command extends Command
{
    protected $signature = 's3:video_get_save';

    protected $description = 'S3に動画データ保存';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $camera_start_on_time = config('const.camera_start_time');
        $camera_end_off_time = config('const.camera_end_time');
        $request_interval = config('const.request_interval');
        $cur_time_object = new \DateTime();
        $cur_time_object->setTimezone(new \DateTimeZone('+0900')); //GMT
        $now = $cur_time_object->format('Y-m-d H:i:s');
        $now_date = $cur_time_object->format('Y-m-d');
        if (strtotime($now_date.' '.$camera_start_on_time) <= strtotime($now) && strtotime($now_date.' '.$camera_end_off_time) >= strtotime($now)) {
            Log::info('カメラチェック開始');
            $cameras = Camera::all();
            if (count($cameras) == 0) {
                return 0;
            }
            $record_start_time_object = clone $cur_time_object;
            $record_start_time_object->sub(new \DateInterval('PT'.(string) 2 * $request_interval.'M'));
            // $record_start_time_object->sub(new \DateInterval('PT'.(string) (2 * $request_interval + 3).'M'));
            $record_start_time = $record_start_time_object->format('c');
            $record_end_time_object = clone $cur_time_object;
            $record_end_time_object->sub(new \DateInterval('PT'.(string) $request_interval.'M'));
            // $record_end_time_object->sub(new \DateInterval('PT'.(string) ($request_interval + 3).'M'));
            $record_end_time = $record_end_time_object->format('c');

            foreach ($cameras as $camera) {
                if ($camera->contract_no == null) {
                    continue;
                }
                $safie_service = new SafieApiService($camera->contract_no);

                //メディアファイル 作成要求削除(s3に保存されたもの)
                Log::info('メディアファイル 作成要求削除(s3に保存されたもの)ーーーー');
                $this->deleteOldRequests($camera->contract_no);

                //メディアファイル作成要求一覧取得・S3に保存--------------------------------
                Log::info('メディアファイル作成要求一覧取得・S3に保存ーーーー');
                $this->saveMedia($camera->camera_id, $camera->contract_no, $camera->id);
                //----------------------------------------------------------------------
                //メディアファイル作成要求--------------------------------
                Log::info('メディアファイル作成要求--------------------------------');
                $request_id = $safie_service->makeMediaFile($camera->camera_id, $record_start_time, $record_end_time);
                Log::info('request_id = '.$request_id);
                if ($request_id > 0) {
                    $this->createS3History($request_id, $record_start_time_object, $record_end_time_object, $camera->camera_id, $camera->contract_no);
                }
                //--------------------------------------------------------
            }
        }

        return 0;
    }

    public function deleteOldRequests($contract_no = null)
    {
        $query = S3VideoHistory::query()->where('status', '=', 2);
        if ($contract_no != null) {
            $query->where('contract_no', $contract_no);
        }
        $data = $query->orderBy('updated_at')->get();
        $safie_service = new SafieApiService($contract_no);
        if (count($data) > 0) {
            foreach ($data as $item) {
                $res = $safie_service->deleteMediaFile($item->device_id, $item->request_id);
                if ($res == 200 || $res == 404) {
                    $item->status = 3;
                    $item->save();
                }
            }
        }
    }

    public function createS3History($request_id, $record_start_time_object, $record_end_time_object, $device_id = null, $contract_no = null)
    {
        $record = new S3VideoHistory();
        $record->request_id = $request_id;
        $record->device_id = $device_id == null ? 'FvY6rnGWP12obPgFUj0a' : $device_id;
        $record->start_time = $record_start_time_object->format('Y-m-d H:i:s');
        $record->end_time = $record_end_time_object->format('Y-m-d H:i:s');
        $record->contract_no = $contract_no;
        $record->save();
    }

    public function saveMedia($device_id = null, $contract_no = null, $id_camera = 0)
    {
        $device_id = $device_id == null ? 'FvY6rnGWP12obPgFUj0a' : $device_id;
        $data = S3VideoHistory::query()->where('device_id', $device_id)
            ->where('contract_no', $contract_no)
            ->where('status', '!=', 2)
            ->where('status', '!=', 3)
            ->orderByDesc('updated_at')->limit(10)->get();
        $safie_service = new SafieApiService($contract_no);
        foreach ($data as $item) {
            Log::info('メディアファイル 作成要求取得ーーーー');
            $media_status = $safie_service->getMediaFileStatus($device_id, $item->request_id);
            if ($media_status != null) {
                if ($media_status['state'] == 'AVAILABLE') {
                    if ($item->status == 0) {
                        $item->status = 1;
                        $item->save();
                    }
                    $video_data = $safie_service->downloadMediaFile($media_status['url']);
                    if ($video_data != null) {
                        Log::info('メディアファイル ダウンロード成功ーーーー');
                        $start_time = $item->start_time;
                        $end_time = $item->end_time;
                        $start_date = date('Ymd', strtotime($start_time));
                        $file_name = date('YmdHis', strtotime($start_time)).'_'.date('YmdHis', strtotime($end_time)).'.mp4';
                        Storage::disk('s3')->put($device_id.'\\'.$start_date.'\\'.$file_name, $video_data);
                        $item->status = 2;
                        $item->file_path = $device_id.'/'.$start_date.'/'.$file_name;
                        $item->save();
                        //request rule data to AI--------
                        $aws_url = 'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/';
                        $movie_path = $aws_url.$device_id.'/'.$start_date.'/'.$file_name;
                        $this->reqeuestToAI($device_id, $id_camera, $movie_path);
                        //-------------------------------
                    }
                }
            }
        }
    }

    public function reqeuestToAI($device_id, $id_camera, $movie_path = null)
    {
        $url = '';
        $header = [
            'Content-Type: application/json',
        ];
        //2. 危険エリア侵入検知解析リクエスト（BI→AI） api/v1/danger-zone/register-camera
        $rules = DangerService::getRulesByCameraID($id_camera);
        if (count($rules) > 0) {
            $params = [];
            foreach ($rules as $rule) {
                if (!isset($params['camera_info'])) {
                    $params['camera_info'] = [];
                }
                $params['camera_info']['camera_id'] = $device_id;
                if (!isset($params['movie_info'])) {
                    $params['movie_info'] = [];
                }
                $params['movie_info']['movie_path'] = $movie_path;
                // $params['movie_info']['movie_path'] = 'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_danger/20220311/20220311100323_20220311100822.mp4';
                if (!isset($params['rect_info'])) {
                    $params['rect_info'] = [];
                }

                $params['rect_info']['rect_id'] = (string) $rule->id;
                // $params['rect_info']['rect_point_array'] = json_decode($rule->points);
                $sample_point_data = '[{"x": 1041,"y": 238,"id": 0},{"x": 1001,"y": 433,"id": 1},{"x": 581,"y": 231,"id": 2},{"x": 707,"y": 114,"id": 3}]';
                $params['rect_info']['rect_point_array'] = json_decode($sample_point_data);
                $action_data = [];
                foreach (json_decode($rule->action_id) as $action_code) {
                    $action_data[] = (int) $action_code;
                }
                $params['rect_info']['action_id'] = $action_data;
                $params['priority'] = 1;
                Log::info('危険エリア侵入検知解析リクエスト（BI→AI）開始ーーーー');
                $url = 'https://52.192.123.36/api/v1/danger-zone/register-camera';
                $this->sendPostApi($url, $header, $params, 'json');
            }
        }
        //--------------------------------------

        //４．ピット入退場解析リクエスト（BI→AI） /api/v1/pit/register-camera
        $rules = PitService::getRulesByCameraID($id_camera);
        if (count($rules) > 0) {
            if (count($rules) > 0) {
                $params = [];
                foreach ($rules as $rule) {
                    if (!isset($params['camera_info'])) {
                        $params['camera_info'] = [];
                    }
                    $params['camera_info']['camera_id'] = $device_id;
                    if (!isset($params['movie_info'])) {
                        $params['movie_info'] = [];
                    }
                    $params['movie_info']['movie_path'] = $movie_path;
                    // $params['movie_info']['movie_path'] = 'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_pit/20220707/20220707153430_20220707153455.mp4';
                    if (!isset($params['rect_info'])) {
                        $params['rect_info'] = [];
                    }
                    $params['rect_info']['rect_id'] = (string) $rule->id;
                    $params['rect_info']['entrance_rect_point_array'] = json_decode($rule->red_points);
                    $params['rect_info']['exit_rect_point_array'] = json_decode($rule->blue_points);
                    $params['priority'] = 1;

                    Log::info('ピット入退場解析リクエスト（BI→AI）開始ーーーー');
                    $url = 'https://52.192.123.36/api/v1/pit/register-camera';
                    $this->sendPostApi($url, $header, $params, 'json');
                }
            }
        }
        //--------------------------------------
        //６．棚乱れ解析リクエスト（BI→AI） /api/v1/shelf-theft/register-camera
        $rules = ShelfService::getRulesByCameraID($id_camera);
        if (count($rules) > 0) {
            if (count($rules) > 0) {
                $params = [];
                foreach ($rules as $rule) {
                    if (!isset($params['camera_info'])) {
                        $params['camera_info'] = [];
                    }
                    $params['camera_info']['camera_id'] = $device_id;
                    if (!isset($params['movie_info'])) {
                        $params['movie_info'] = [];
                    }
                    $params['movie_info']['movie_path'] = $movie_path;
                    // $params['movie_info']['movie_path'] = 'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_shelf/20220707/20220707153430_20220707153455.mp4';
                    if (!isset($params['rect_info'])) {
                        $params['rect_info'] = [];
                    }
                    $rect_param = [];
                    $rect_param['rect_id'] = (string) $rule->id;
                    $rect_param['rect_point_array'] = json_decode($rule->points);
                    $params['rect_info'][] = $rect_param;
                    $params['priority'] = 1;
                }
                Log::info('棚乱れ解析リクエスト（BI→AI）開始ーーーー');
                $url = 'https://52.192.123.36/api/v1/shelf-theft/register-camera';
                $this->sendPostApi($url, $header, $params, 'json');
            }
        }
        //--------------------------------------

        //９．大量盗難解析リクエスト（BI→AI） /api/v1/hanger-counter/register-camera
        $rules = ThiefService::getRulesByCameraID($id_camera);
        if (count($rules) > 0) {
            if (count($rules) > 0) {
                $params = [];
                foreach ($rules as $rule) {
                    if (!isset($params['camera_info'])) {
                        $params['camera_info'] = [];
                    }
                    $params['camera_info']['camera_id'] = $device_id;
                    if (!isset($params['movie_info'])) {
                        $params['movie_info'] = [];
                    }
                    $params['movie_info']['movie_path'] = $movie_path;
                    // $params['movie_info']['movie_path'] = 'https://s3-ap-northeast-1.amazonaws.com/ridge-bi-s3/test_thief/20220311/20220311100323_20220311100346.mp4';
                    if (!isset($params['rect_info'])) {
                        $params['rect_info'] = [];
                    }
                    $rect_param = [];
                    $rect_param['rect_id'] = (string) $rule->id;
                    $rect_param['color_code'] = $rule->hanger;
                    $rect_param['rect_point_array'] = json_decode($rule->points);
                    $params['rect_info'][] = $rect_param;
                    $params['priority'] = 1;
                }
                Log::info('大量盗難解析リクエスト（BI→AI）開始ーーーー');
                $url = 'https://52.192.123.36/api/v1/hanger-counter/register-camera';
                $this->sendPostApi($url, $header, $params, 'json');
            }
        }
        //--------------------------------------
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

            return $response_return;
        } else {
            return null;
        }
    }
}
