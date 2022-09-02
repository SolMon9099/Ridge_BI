@extends('admin.layouts.app')

@section('content')
<div id="wrapper">
    <div id="r-content">
	    <div class="sp-ma">
            <h2 class="title">最近の検知</h2>
            <ul class="kenchi-list">
                @foreach ($danger_detections as $item)
                <?php
                    $video_path = '';
                    $video_path .= asset('storage/video/').'/';
                    $video_path .= $item->video_file_path;

                    $thumb_path = asset('storage/thumb/').'/'.$item->thumb_img_path;
                ?>
                <li>
                    <div class="movie" video-path = '{{$video_path}}'>
                        <a data-target="movie0000" onclick="videoPlay('{{$video_path}}')" class="modal-open setting2 play">
                            <img src="{{$thumb_path}}"/>
                        </a>
                        <div class="cap">
                            <time>{{date('Y/m/d H:i', strtotime($item->starttime))}}</time>
                        </div>
                    </div>
                    <div class="text">
                        <p class="camera-id">カメラID:{{$item->camera_no}}</p>
                        <ul class="pit-list">
                            <li>
                                <h2 class="icon-map">設置場所</h2>
                                <dl>
                                    <dt>設置エリア</dt>
                                    <dd>{{$item->location_name}}</dd>
                                </dl>
                                <dl>
                                    <dt>設置フロア</dt>
                                    <dd>{{$item->floor_number}}</dd>
                                </dl>
                                <dl>
                                    <dt>設置場所</dt>
                                    <dd>{{$item->installation_position}}</dd>
                                </dl>
                            </li>
                            <li>
                                <h2 class="icon-condition">検知条件</h2>
                                <dl>
                                    <dt><p>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action_cond_statement')[$item->detection_action_id] : ''}}</p></dt>
                                    <dd>1人</dd>
                                </dl>
                            </li>
                            <li>
                                <h2 class="icon-content">検知内容</h2>
                                <p>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action_statement')[$item->detection_action_id] : ''}}</p>
                            </li>
                            <li>
                                <h2 class="icon-rule">ルール</h2>
                                <dl>
                                    <dt>{{$item->detection_action_id}}</dt>
                                    <dd>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action')[$item->detection_action_id] : ''}}</dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
	</div>
</div>
<!--MODAL -->
<div id="movie0000" class="modal-content">
    <div class="textarea">
        <div class="v">
            <video id = 'video-container' src = '' type= 'video/mp4' controls>
            </video>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<script>
    function videoPlay(path){
        var video = document.getElementById('video-container');
        video.pause();
        $('#video-container').attr('src', path);
        video.play();
    }
</script>
@endsection
