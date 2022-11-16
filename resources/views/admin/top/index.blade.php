@extends('admin.layouts.app')
@section('content')
<link href="{{ asset('assets/admin/css/gridstack.min.css') }}?{{ Carbon::now()->format('Ymdhis') }}" rel="stylesheet">
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .sp-ma{
        padding-top: 50px;
    }
    .title-area{
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .add-widget{
        padding-top:25px;
    }
    .add-widget a{
        color:#0062de;
        text-decoration: underline;
    }
    .menu-sub-li{
        padding-left: 15px;
        font-size: 14px;
        padding-top: 5px;
    }
    .user-menu{
        display: none;
        position: absolute;
        padding: 20px;
        width: 220px;
        right: 0;
        top: 50px;
        background: #FFF;
        box-shadow: 0 0 10px #ccc;
        border-radius: 5px;
        z-index: 99999;
    }
    .user-menu h2{
        opacity: 0.5;
        margin-top:10px;
    }
    .grid-stack{
        width:100%;
        margin-top:15px;
        background: #DDEBF7;
    }
    .grid-stack-item-content {
        background-color: white;
        padding: 5px;
        overflow-y:hidden!important;
    }
    .grid-contents{
        height: calc(100% - 40px);
        position: relative;
        overflow-y: auto;
    }
    .grid-contents::-webkit-scrollbar {
        width: 7px;
    }

    .grid-contents::-webkit-scrollbar-thumb {
        background: lightgray;
        border-radius: 3px;
    }
    .grid-contents::-webkit-scrollbar-track {
        background:white;
    }
    .ui-resizable-handle{
        /* right:25px!important; */
        transform: rotate(45deg)!important;
        background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDUxMi4wNCA1MTIuMDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMi4wNCA1MTIuMDQ7ZmlsbDogcmdiYSgwLCA5OCwgMjIyLCAxKTsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik01MDguOTMzLDI0OC4zNTNMNDAyLjI2NywxNDEuNjg3Yy00LjI2Ny00LjA1My0xMC45ODctMy45NDctMTUuMDQsMC4yMTNjLTMuOTQ3LDQuMTYtMy45NDcsMTAuNjY3LDAsMTQuODI3DQoJCQlsODguNDI3LDg4LjQyN0gzNi40bDg4LjQyNy04OC40MjdjNC4wNTMtNC4yNjcsMy45NDctMTAuOTg3LTAuMjEzLTE1LjA0Yy00LjE2LTMuOTQ3LTEwLjY2Ny0zLjk0Ny0xNC44MjcsMEwzLjEyLDI0OC4zNTMNCgkJCWMtNC4xNiw0LjE2LTQuMTYsMTAuODgsMCwxNS4wNEwxMDkuNzg3LDM3MC4wNmM0LjI2Nyw0LjA1MywxMC45ODcsMy45NDcsMTUuMDQtMC4yMTNjMy45NDctNC4xNiwzLjk0Ny0xMC42NjcsMC0xNC44MjcNCgkJCUwzNi40LDI2Ni41OTNoNDM5LjE0N0wzODcuMTIsMzU1LjAyYy00LjI2Nyw0LjA1My00LjM3MywxMC44OC0wLjIxMywxNS4wNGM0LjA1Myw0LjI2NywxMC44OCw0LjM3MywxNS4wNCwwLjIxMw0KCQkJYzAuMTA3LTAuMTA3LDAuMjEzLTAuMjEzLDAuMjEzLTAuMjEzbDEwNi42NjctMTA2LjY2N0M1MTMuMDkzLDI1OS4zNCw1MTMuMDkzLDI1Mi41MTMsNTA4LjkzMywyNDguMzUzeiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K')!important;
    }
    .none{
        display: none;
    }
    .video-play{
        display: none;
        position: absolute;
        top: 25px;
        width: 80%;
        left: 20px;
    }
    .list-video{
        position: relative;
        top:0;
    }
    .video-notice.none{
        color:black;
    }
    .video-open{
        display: inline-block;
        cursor: pointer;
    }
    .video-open.setting2{
        background: transparent;
        color: #555;
        width: 100%;
        border-radius: 5px;
        padding: 0;
        box-sizing: border-box;
        position: relative;
    }
    .video-open:before {
        content: "▲";
        display: inline-block;
        transform: rotate(90deg);
        font-size: 10px;
        margin: 0 5px 0 0;
    }
    .video-open.setting2:before{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%) rotate(90deg);
        font-size: 20px;
        background: #FFF;
        width: 50px;
        height: 50px;
        line-height: 50px;
        text-align: center;
        vertical-align: middle;
        border-radius: 50%;
        display: block;
        box-shadow: 0px -1px 10px 0px rgb(0 0 0 / 50%);
    }
    .magnify-icon{
        position: absolute;
        left:10px;
        top:7px;
        cursor: pointer;
    }
    .gear-block{
        position: absolute;
        right: 5px;
        top: 7px;
        background: white;
    }
    .gear-block > button{
        background: transparent;
        border:none;
    }
    .close-gear-icon.block-gear{
        width:18px;
        margin-top:-8px;
    }
    .close-gear-icon.block-gear:before{
        width:18px;
    }
    .close-gear-icon.block-gear:after{
        width:18px;
    }
    .live-stream-title{
        font-size: 18px;
        width:100%;
        text-align: center;
        padding-left:24px;
        padding-right:24px;
    }
    .camera-id{
        font-size:14px;
    }
    .image-container{
        position: absolute;
        z-index: 1;
    }
    .streaming-video{
        height:80%;
    }
    .movie{
        width: 70%;
        padding-top: 20px;
        padding-left: 20px;
    }
    .cap{
        padding-left: 20px;
    }
    .gear-menu{
        display: none;
        position: relative;
    }
    .hidden-gear-box{
        position: absolute;
        right: 0;
        top:20px;
        padding: 15px;
        width:180px;

        background: #FFF;
        box-shadow: 0 0 10px #CCC;
        border-radius:5px;
        z-index: 1000;
    }
    .hidden-gear-box > li{
        margin: 0 0 0 0;
        cursor: pointer;
        padding-top: 3px;
        padding-bottom: 3px;
    }
    .hidden-gear-box > li:hover{
        background: lightblue;
    }
    .danger-detect-list{
        margin-top:8px;
    }
    .danger-detect-list > tbody > tr > td{
        border: 1px solid lightgray;
        padding:4px;
    }
    .danger-detect-list > thead > tr > th{
        border: 1px solid lightgray;
        padding:5px;
        text-align: center;
    }
    .time{
        width:10%;
    }
    .area{
        width:20%;
    }
    .location{
        width:20%;
    }
    .action{
        width:15%;
    }
    .list-table > thead{
        background: #0062de;
        color: white;
    }
    .list-table th{
        text-align: center;
    }
    .list-table > tbody > tr:nth-child(even){
        background: #edf3f8;
    }
    .list-play-icon::before{
        font-size: 15px;
        background: #FFF;
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        vertical-align: middle;
        border-radius: 50%;
        display: block;
        box-shadow: 0px -1px 10px 0px rgb(0 0 0 / 50%);

    }
    .extentsion-content{
        background: white;
        width: 80%;
        max-width: 1100px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        box-sizing: border-box;
        transition: all 0.5s ease-in-out;
        height: 95%;
        padding:20px;
        overflow: auto;
    }
    .extentsion-content .grid-contents{
        height: calc(100% - 40px);
    }
    .extentsion-content .streaming-video{
        width:960px;
        height:540px;
        margin:auto;
    }
    .extentsion-content .no-data{
        margin-top:0px;
        padding-top:40px;
        border:none;
    }
    .extentsion-content .movie{
        width: 90%;
    }
    .extentsion-content .video-play{
        width:96%;
    }
    .extentsion-content .list-table{
        width:96%;
    }
    .modal-title{
        margin-bottom: 10px;
        font-size:20px;
    }
    .search-period-area{
        font-size:14px;
        text-align: center;
        margin-top: 3px;
        margin-bottom: 3px;
    }
    .period-select-buttons{
        text-align: right;
        margin-top: 3px;
    }
    .period-select-buttons > button{
        font-size: 12px;
        background:lightcyan;
    }
    .prev, .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        width: auto;
        padding-left: 15px;
        padding-right: 15px;
        padding-top:8px;
        padding-bottom: 8px;
        font-weight: bold;
        font-size: 18px;
        transition: 0.6s ease;
        user-select: none;
    }
    .prev{
        left:7px;
    }
    .next {
        right: -20px;
    }
    .prev:hover, .next:hover {
        background-color:lightcoral;
        color:white;
        border-radius: 20px;
    }
    .graph-area{
        position: relative;
        width:calc(100% - 20px);
    }
    .graph-canvas{
        cursor: pointer;
    }
</style>
<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $admin_flag = ($login_user->authority_id == config('const.authorities_codes.admin'));
    $general_user_flag = !($super_admin_flag | $admin_flag);
    $headers = isset($login_user->header_menu_ids)?explode(",", $login_user->header_menu_ids):[];
    $manager_allowed_pages = $login_user->manager_allowed_pages;
    $top_allowed_pages = ['TOP', '検知リスト', '過去グラフ'];
?>

<div id="wrapper">
    <div id="r-content">
	    <div class="sp-ma">
            <div class="title-area">
                <h2 class="title">ダッシュボード</h2>
                <div class="add-widget">
                    <a class="total-menu-letter" onClick="showEditMenu(this)" href="#">表示項目を追加</a>
                </div>
                <ul class="user-menu">
                    @foreach (config('const.header_menus') as $code => $header_name)
                    @if ($super_admin_flag || in_array($code, $headers))
                        <h2>{{$header_name}}</h2>
                        <ul>
                        @foreach (config('const.pages')[$header_name] as $item)
                            <?php $page_id = $item['id']; $url = config('const.page_route_names')[$page_id];?>
                            @if (in_array($item['name'], $top_allowed_pages))
                                @if ($super_admin_flag)
                                    @if (!in_array($url, config('const.super_admin_not_allowed_pages')))
                                        <li class="menu-li"><a href="{{route($url).'?from_top=true'}}">{{$item['name']}}</a></li>
                                        @if($item['name'] == 'TOP')
                                            <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">リアルタイム映像</a></li>
                                            <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">当日グラフ</a></li>
                                            <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">最新の検知</a></li>
                                            @if ($url == 'admin.pit.detail')
                                                <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">入退場履歴</a></li>
                                            @endif
                                        @endif
                                    @endif
                                @else
                                    @if (!$general_user_flag || in_array($url, $manager_allowed_pages))
                                        <li class="menu-li"><a href="{{route($url).'?from_top=true'}}">{{$item['name']}}</a></li>
                                        @if($item['name'] == 'TOP')
                                            <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">リアルタイム映像</a></li>
                                            <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">当日グラフ</a></li>
                                            <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">最新の検知</a></li>
                                            @if ($url == 'admin.pit.detail')
                                                <li class="menu-sub-li"><a href="{{route($url).'?from_top=true'}}">入退場履歴</a></li>
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endforeach
                        </ul>
                    @endif
                    @endforeach
                </ul>
            </div>
                {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                    <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                    <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                </svg> --}}
            @include('admin.layouts.flash-message')
            <div id ="block-container" style="display: none">
            @if (count($top_blocks) > 0)
                <div class="grid-stack">
                    @foreach ($top_blocks as $key => $item)
                    <div class="grid-stack-item" data-id = "{{$item->id}}" gs-x="{{$item->gs_x}}" gs-y="{{$item->gs_y}}" gs-w="{{$item->gs_w}}" gs-h="{{$item->gs_h}}">
                        <div class="grid-stack-item-content">
                            <div class="magnify-icon" onclick="showExtensionModal(this, '{{config('const.top_block_titles')[$item->block_type]}}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 98, 222, 1);transform: ;msFilter:;">
                                    <path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z"/>
                                </svg>
                            </div>
                            <div class="gear-block">
                                <button type="" class="top-gear" onclick="toggleMenu(this, {{$item->id}})">
                                    <svg class='gear-button' xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="fill: rgba(0, 98, 222, 1);transform: ;msFilter:;">
                                        <path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"></path>
                                        <path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"></path>
                                    </svg>
                                    <div class="close-gear-icon block-gear"></div>
                                </button>
                            </div>
                            <div class="gear-menu" data-menu-id = "{{$item->id}}">
                                <ul class="hidden-gear-box">
                                    @foreach (config('const.top_block_gears')[$item->block_type] as $key_name => $gear_name)
                                        <li onclick="gearClick({{json_encode($item)}}, '{{$key_name}}')">{{$gear_name}}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="live-stream-title">{{config('const.top_block_titles')[$item->block_type]}}</div>
                            <div class='grid-contents'>
                                @if($item->block_type == config('const.top_block_type_codes')['live_video_danger']
                                    || $item->block_type == config('const.top_block_type_codes')['live_video_pit']
                                    || $item->block_type == config('const.top_block_type_codes')['live_video_shelf']
                                    || $item->block_type == config('const.top_block_type_codes')['live_video_thief']
                                )
                                    @if(isset($item->cameras) && count($item->cameras) > 0 && isset($item->selected_camera))
                                        <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                        <div class="camera-id">カメラID： {{$item->selected_camera['serial_no']}}</div>
                                        <div id={{"image_container_".$item->id}} class="image-container"></div>
                                        <div class="streaming-video" id = {{'streaming_video_'.$item->id}}>
                                            <safie-streaming-player data-camera-id='{{$item->selected_camera['camera_id']}}' data-token='{{$item->selected_camera['access_token']}}'>
                                            </safie-streaming-player>
                                        </div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['recent_detect_danger'])
                                    @if(isset($item->danger_detection))
                                        <?php
                                            $video_path = '';
                                            $video_path .= asset('storage/video/').'/';
                                            $video_path .= $item->danger_detection['video_file_path'];

                                            if (isset($item->danger_detection['thumb_img_path']) && $item->danger_detection['thumb_img_path'] != ''){
                                                $thumb_path = asset('storage/thumb/').'/'.$item->danger_detection['thumb_img_path'];
                                            } else {
                                                $thumb_path = asset('assets/admin/img/samplepic.png');
                                            }
                                            $video_enabled = false;
                                            if (isset($item->danger_detection['thumb_img_path']) && $item->danger_detection['thumb_img_path'] != '' && isset($item->danger_detection['video_file_path']) && $item->danger_detection['video_file_path'] != ''){
                                                $video_enabled = true;
                                            }
                                        ?>
                                        <div class="camera-id">カメラID：{{$item->danger_detection['serial_no']}}</div>
                                        @if($video_enabled)
                                            <div class="movie" video-path = "{{$video_path}}">
                                                <a data-target="movie0000"
                                                    {{-- onclick="videoPlay(this, '{{$video_path}}')"  --}}
                                                    class="video-open setting2 play">
                                                    <img src="{{$thumb_path}}"/>
                                                </a>
                                            </div>
                                            <video style="" class = 'video-play' src = '{{$video_path}}' type= 'video/mp4' controls></video>
                                        @else
                                            <div class="movie">
                                                <img src="{{$thumb_path}}"/>
                                                <time>検知時点の映像は、Safieのマイページにてご確認ください</time>
                                            </div>
                                        @endif

                                        <div class="cap">検知時間：<time>{{date('Y/m/d H:i', strtotime($item->danger_detection['starttime']))}}</time></div>
                                        <div class="cap">検知条件：
                                            <time>
                                                {{isset($item->danger_detection['detection_action_id']) && $item->danger_detection['detection_action_id'] > 0 ? config('const.action_cond_statement')[$item->danger_detection['detection_action_id']] : ''}}
                                            </time>
                                        </div>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['detect_list_danger'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                        <div class="search-period-area">
                                            <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                                max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                            <span>～</span>
                                            <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                                max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                        </div>
                                    @endif
                                    @if (isset($item->danger_detections) && count($item->danger_detections) > 0)
                                        <video style="" class = 'video-play list-video' src = '' type= 'video/mp4' controls></video>
                                        <p class="video-notice detect-content none"></p>
                                        <p class="video-notice none">動画の30秒あたりが検知のタイミングになります。</p>
                                        <table class="danger-detect-list list-table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>カメラNo</th>
                                                    <th class="time">時間</th>
                                                    <th class="">設置エリア</th>
                                                    <th class="">設置場所</th>
                                                    <th class="action">アクション</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($item->danger_detections as $detection_item)
                                                <?php
                                                    $detection_item = (array)$detection_item;
                                                    $video_path = '';
                                                    $video_path .= asset('storage/video/').'/';
                                                    $video_path .= $detection_item['video_file_path'];
                                                    $video_enabled = false;
                                                    if (isset($detection_item['thumb_img_path']) && $detection_item['thumb_img_path'] != '' && isset($detection_item['video_file_path']) && $detection_item['video_file_path'] != ''){
                                                        $video_enabled = true;
                                                    }
                                                ?>
                                                <tr>
                                                    <td>
                                                        @if($video_enabled)
                                                            <div video-path = "{{$video_path}}">
                                                                <a data-target="movie0000" class="video-open list-play-icon play"></a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{$detection_item['serial_no']}}</td>
                                                    <td>{{date('Y/m/d H:i', strtotime($detection_item['starttime']))}}</td>
                                                    <td>{{$detection_item['location_name']}}</td>
                                                    <td>{{$detection_item['installation_position']}}</td>
                                                    <td>{{$detection_item['detection_action_id'] > 0 ? config('const.action')[$detection_item['detection_action_id']] : ''}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['live_graph_danger'])
                                    @if(isset($item->selected_camera))
                                        <div class="camera-id">カメラID：{{$item->selected_camera['serial_no']}}</div>
                                    @endif
                                    <div class="period-select-buttons">
                                        <?php
                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                        ?>
                                        <button onclick="changeXlength(this, '3')" type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>">3時間</button>
                                        <button onclick="changeXlength(this, '6')" type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>">6時間</button>
                                        <button onclick="changeXlength(this, '12')" type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>">12時間</button>
                                    </div>
                                    <div class="graph-area">
                                        <canvas onclick="location.href='{{route('admin.danger.detail')}}'" id="live_graph_danger" class="graph-canvas"></canvas>
                                        <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                        <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                    </div>
                                @elseif($item->block_type == config('const.top_block_type_codes')['past_graph_danger'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                        <?php
                                            $selected_rule = '';
                                            if (isset($item->selected_rule) && $item->selected_rule > 0){
                                                $selected_rule = $item->selected_rule;
                                            }
                                            $selected_rule_object = null;
                                            if ($selected_rule > 0){
                                                foreach($item->rules as $rule_item){
                                                    if ($rule_item->id == $selected_rule){
                                                        $selected_rule_object = $rule_item;
                                                    }
                                                }
                                            }

                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                            $starttime = date('Y-m-d', strtotime($item->starttime));
                                            $endtime = date('Y-m-d', strtotime($item->endtime));
                                            $search_period = (strtotime($endtime) - strtotime($starttime))/86400;
                                            if ($search_period < 1) {
                                                if (!in_array($time_period, ['3', '6', '12', '24'])){
                                                    $time_period = '3';
                                                }
                                            } else if ($search_period < 7 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 30 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 180 ) {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            } else {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            }
                                        ?>

                                        <div class="camera-id">{{$selected_rule_object != null ? $selected_rule_object->name.'('.$selected_rule_object->serial_no. ')' : ''}}</div>
                                        <div class="search-period-area">
                                            <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                                max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                            <span>～</span>
                                            <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                                max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                        </div>
                                        <div class="period-select-buttons">
                                            @if ($search_period < 1)
                                                <button type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>"  onclick="changeXlength(this, '3')">3時間</button>
                                                <button type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'6')">6時間</button>
                                                <button type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'12')">12時間</button>
                                                <button type="button" class="<?php echo $time_period == '24' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'24')">24時間</button>
                                            @elseif ($search_period < 7)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 30)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 180)
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @else
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @endif
                                        </div>

                                        <div class="graph-area">
                                            <input type="hidden" class="min-time" value="{{date('Y-m-d H:i:s', strtotime($item->starttime))}}"/>
                                            <a class="prev" onclick="moveXRange(this, -1)">❮</a>
                                            <a class="next" onclick="moveXRange(this, 1)">❯</a>
                                            <canvas onclick="location.href='{{route('admin.danger.past_analysis')}}'+'?change_params=change&starttime=' + '{{date('Y-m-d', strtotime($item->starttime))}}'
                                                + '&endtime='+'{{date('Y-m-d', strtotime($item->endtime))}}'+'&selected_rule='+{{$selected_rule}}+'&time_period='+'{{$time_period}}'"
                                                id="past_graph_danger" class="graph-canvas"></canvas>
                                            <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                            <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                            <input type="hidden" class="search_period" value="{{$search_period}}"/>
                                        </div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['pit_history'])
                                    @if(isset($item->selected_camera))
                                        <div class="camera-id">カメラID：{{$item->selected_camera['serial_no']}}</div>
                                    @endif
                                    @if (isset($item->pit_detections) && count($item->pit_detections) > 0)
                                        <table class="pit-detect-list list-table">
                                            <thead>
                                                <tr>
                                                    <th>時間</th>
                                                    <th>検知条件</th>
                                                    <th>人数変化</th>
                                                    <th>ピット内人数</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach (array_reverse($item->pit_detections) as $detection_item)
                                                <tr>
                                                    <td>{{date('Y-m-d H:i:s', strtotime($detection_item['starttime']))}}</td>
                                                    <td>{{$detection_item['nb_entry'] > $detection_item['nb_exit'] ? '入場' : '退場'}} </td>
                                                    <td><span class="{{$detection_item['nb_entry'] > $detection_item['nb_exit'] ? 'f-red' : 'f-blue'}}">{{$detection_item['nb_entry'] - $detection_item['nb_exit']}}</span></td>
                                                    <td>{{$detection_item['sum_in_pit']}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif

                                @elseif($item->block_type == config('const.top_block_type_codes')['recent_detect_pit'])
                                    @if(isset($item->pit_detection))
                                        <?php
                                            $video_path = '';
                                            $video_path .= asset('storage/video/').'/';
                                            $video_path .= $item->pit_detection->video_file_path;

                                            if (isset($item->pit_detection->thumb_img_path) && $item->pit_detection->thumb_img_path != ''){
                                                $thumb_path = asset('storage/thumb/').'/'.$item->pit_detection->thumb_img_path;
                                            } else {
                                                $thumb_path = asset('assets/admin/img/samplepic.png');
                                            }
                                            $video_enabled = false;
                                            if (isset($item->pit_detection->thumb_img_path) && $item->pit_detection->thumb_img_path != '' && isset($item->pit_detection->video_file_path) && $item->pit_detection->video_file_path != ''){
                                                $video_enabled = true;
                                            }
                                        ?>
                                        <div class="camera-id">カメラID：{{$item->pit_detection->serial_no}}</div>
                                        @if($video_enabled)
                                            <div class="movie" video-path = "{{$video_path}}">
                                                <a data-target="movie0000"
                                                    {{-- onclick="videoPlay(this, '{{$video_path}}')"  --}}
                                                    class="video-open setting2 play">
                                                    <img src="{{$thumb_path}}"/>
                                                </a>
                                            </div>
                                            <video style="" class = 'video-play' src = '{{$video_path}}' type= 'video/mp4' controls></video>
                                        @else
                                            <div class="movie">
                                                <img src="{{$thumb_path}}"/>
                                                <time>検知時点の映像は、Safieのマイページにてご確認ください</time>
                                            </div>
                                        @endif

                                        <div class="cap">検知時間：<time>{{date('Y/m/d H:i', strtotime($item->pit_detection->detect_time))}}</time></div>
                                        <div class="cap">検知条件：<time>{{$item->pit_detection->min_members.'人以上/'.$item->pit_detection->max_permission_time.'分超過'}}</time></div>
                                        <div class="cap">{{"　　　　　"}}<time>ピット内人数({{$item->pit_detection->sum_in_pit.'人'}})</time></div>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['detect_list_pit'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                    <div class="search-period-area">
                                        <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                            max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                            type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                        <span>～</span>
                                        <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                            max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                            type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                    </div>
                                    @endif
                                    @if (isset($item->pit_detections) && count($item->pit_detections) > 0)
                                        <video style="" class = 'video-play list-video' src = '' type= 'video/mp4' controls></video>
                                        <p class="video-notice detect-content none"></p>
                                        <p class="video-notice none">動画の30秒あたりが検知のタイミングになります。</p>
                                        <table class="pit-detect-list list-table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>カメラNo</th>
                                                    <th class="time">時間</th>
                                                    <th>検知条件</th>
                                                    <th>ピット内人数</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($item->pit_detections as $detection_item)
                                                <?php
                                                    $video_path = '';
                                                    $video_path .= asset('storage/video/').'/';
                                                    $video_path .= $detection_item['video_file_path'];
                                                    $video_enabled = false;
                                                    if (isset($detection_item['thumb_img_path']) && $detection_item['thumb_img_path'] != '' && isset($detection_item['video_file_path']) && $detection_item['video_file_path'] != ''){
                                                        $video_enabled = true;
                                                    }
                                                ?>
                                                <tr>
                                                    <td>
                                                        @if($video_enabled)
                                                            <div video-path = "{{$video_path}}">
                                                                <a data-target="movie0000" class="video-open list-play-icon play"></a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{$detection_item['serial_no']}}</td>
                                                    <td>{{date('Y/m/d H:i', strtotime($detection_item['detect_time']))}}</td>
                                                    <td>{{$detection_item['min_members'].'人以上/'. $detection_item['max_permission_time'].'分超過'}}</td>
                                                    <td>{{isset($detection_item['sum_in_pit']) ? $detection_item['sum_in_pit'].'人' : ''}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif ($item->block_type == config('const.top_block_type_codes')['live_graph_pit'])
                                    @if(isset($item->selected_camera))
                                        <div class="camera-id">カメラID：{{$item->selected_camera['serial_no']}}</div>
                                    @endif
                                    <div class="period-select-buttons">
                                        <?php
                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                        ?>
                                        <button onclick="changeXlength(this, '3')" type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>">3時間</button>
                                        <button onclick="changeXlength(this, '6')" type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>">6時間</button>
                                        <button onclick="changeXlength(this, '12')" type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>">12時間</button>
                                    </div>
                                    <div class="graph-area">
                                        <canvas onclick="location.href='{{route('admin.pit.detail')}}'" id="live_graph_pit" class="graph-canvas"></canvas>
                                        <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                        <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                    </div>
                                @elseif ($item->block_type == config('const.top_block_type_codes')['past_graph_pit'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                        <?php
                                            $selected_rule = '';
                                            if (isset($item->selected_rule) && $item->selected_rule > 0){
                                                $selected_rule = $item->selected_rule;
                                            }
                                            $selected_rule_object = null;
                                            if ($selected_rule > 0){
                                                foreach($item->rules as $rule_item){
                                                    if ($rule_item->id == $selected_rule){
                                                        $selected_rule_object = $rule_item;
                                                    }
                                                }
                                            }
                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                            $starttime = date('Y-m-d', strtotime($item->starttime));
                                            $endtime = date('Y-m-d', strtotime($item->endtime));
                                            $search_period = (strtotime($endtime) - strtotime($starttime))/86400;
                                            if ($search_period < 1) {
                                                if (!in_array($time_period, ['3', '6', '12', '24'])){
                                                    $time_period = '3';
                                                }
                                            } else if ($search_period < 7 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 30 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 180 ) {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            } else {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            }
                                        ?>
                                        <div class="camera-id">{{$selected_rule_object != null ? $selected_rule_object->name.'('.$selected_rule_object->serial_no. ')' : ''}}</div>
                                        <div class="search-period-area">
                                            <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                                max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                            <span>～</span>
                                            <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                                max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                        </div>
                                        <div class="period-select-buttons">
                                            @if ($search_period < 1)
                                                <button type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>"  onclick="changeXlength(this, '3')">3時間</button>
                                                <button type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'6')">6時間</button>
                                                <button type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'12')">12時間</button>
                                                <button type="button" class="<?php echo $time_period == '24' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'24')">24時間</button>
                                            @elseif ($search_period < 7)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 30)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 180)
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @else
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @endif
                                        </div>
                                        <div class="graph-area">
                                            <input type="hidden" class="min-time" value="{{date('Y-m-d H:i:s', strtotime($item->starttime))}}"/>
                                            <a class="prev" onclick="moveXRange(this, -1)">❮</a>
                                            <a class="next" onclick="moveXRange(this, 1)">❯</a>
                                            <canvas onclick="location.href='{{route('admin.pit.past_analysis')}}'+'?change_params=change&starttime=' + '{{date('Y-m-d', strtotime($item->starttime))}}'
                                                + '&endtime='+'{{date('Y-m-d', strtotime($item->endtime))}}'+'&time_period='+'{{$time_period}}'+'&selected_rule='+'{{$selected_rule}}'"
                                                onclick="location.href='{{route('admin.pit.past_analysis')}}'" id="past_graph_pit" class="graph-canvas"></canvas>
                                            <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                            <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                            <input type="hidden" class="search_period" value="{{$search_period}}"/>
                                        </div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['recent_detect_shelf'])
                                    @if(isset($item->shelf_detection))
                                        <?php
                                            $video_path = '';
                                            $video_path .= asset('storage/video/').'/';
                                            $video_path .= $item->shelf_detection['video_file_path'];

                                            $thumb_path = asset('storage/thumb/').'/'.$item->shelf_detection['thumb_img_path'];
                                        ?>
                                        <div class="camera-id">カメラID：{{$item->shelf_detection['serial_no']}}</div>
                                        <div class="movie" video-path = "{{$video_path}}">
                                            <a data-target="movie0000"
                                                {{-- onclick="videoPlay(this, '{{$video_path}}')"  --}}
                                                class="video-open setting2 play">
                                                <img src="{{$thumb_path}}"/>
                                            </a>
                                        </div>
                                        <video style="" class = 'video-play' src = '{{$video_path}}' type= 'video/mp4' controls></video>
                                        <div class="cap">検知時間：<time>{{date('Y/m/d H:i', strtotime($item->shelf_detection['starttime']))}}</time></div>
                                        <div class="cap">ルール：<time>{{isset($item->shelf_detection['rule_name']) ? $item->shelf_detection['rule_name'] : ''}}</time></div>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['detect_list_shelf'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                        <div class="search-period-area">
                                            <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                                max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                            <span>～</span>
                                            <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                                max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                        </div>
                                    @endif
                                    @if (isset($item->shelf_detections) && count($item->shelf_detections) > 0)
                                        <video style="" class = 'video-play list-video' src = '' type= 'video/mp4' controls></video>
                                        <p class="video-notice detect-content none"></p>
                                        <p class="video-notice none">動画の30秒あたりが検知のタイミングになります。</p>
                                        <table class="shelf-detect-list list-table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>カメラNo</th>
                                                    <th class="time">時間</th>
                                                    <th class="">設置エリア</th>
                                                    <th class="">設置場所</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($item->shelf_detections as $detection_item)
                                                <?php
                                                    $detection_item = (array)$detection_item;
                                                    $video_path = '';
                                                    $video_path .= asset('storage/video/').'/';
                                                    $video_path .= $detection_item['video_file_path'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div video-path = "{{$video_path}}">
                                                            <a data-target="movie0000" class="video-open list-play-icon play"></a>
                                                        </div>
                                                    </td>
                                                    <td>{{$detection_item['serial_no']}}</td>
                                                    <td>{{date('Y/m/d H:i', strtotime($detection_item['starttime']))}}</td>
                                                    <td>{{$detection_item['location_name']}}</td>
                                                    <td>{{$detection_item['installation_position']}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['live_graph_shelf'])
                                    @if(isset($item->selected_camera))
                                        <div class="camera-id">カメラID：{{$item->selected_camera['serial_no']}}</div>
                                    @endif
                                    <div class="period-select-buttons">
                                        <?php
                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                        ?>
                                        <button onclick="changeXlength(this, '3')" type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>">3時間</button>
                                        <button onclick="changeXlength(this, '6')" type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>">6時間</button>
                                        <button onclick="changeXlength(this, '12')" type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>">12時間</button>
                                    </div>
                                    <div class="graph-area">
                                        <canvas onclick="location.href='{{route('admin.shelf.detail')}}'" id="live_graph_shelf" class="graph-canvas"></canvas>
                                        <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                        <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                    </div>
                                @elseif($item->block_type == config('const.top_block_type_codes')['past_graph_shelf'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                        <?php
                                            $selected_rule = '';
                                            if (isset($item->selected_rule) && $item->selected_rule > 0){
                                                $selected_rule = $item->selected_rule;
                                            }
                                            $selected_rule_object = null;
                                            if ($selected_rule > 0){
                                                foreach($item->rules as $rule_item){
                                                    if ($rule_item->id == $selected_rule){
                                                        $selected_rule_object = $rule_item;
                                                    }
                                                }
                                            }

                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                            $starttime = date('Y-m-d', strtotime($item->starttime));
                                            $endtime = date('Y-m-d', strtotime($item->endtime));
                                            $search_period = (strtotime($endtime) - strtotime($starttime))/86400;
                                            if ($search_period < 1) {
                                                if (!in_array($time_period, ['3', '6', '12', '24'])){
                                                    $time_period = '3';
                                                }
                                            } else if ($search_period < 7 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 30 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 180 ) {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            } else {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            }
                                        ?>

                                        <div class="camera-id">{{$selected_rule_object != null ? $selected_rule_object->name.'('.$selected_rule_object->serial_no. ')' : ''}}</div>
                                        <div class="search-period-area">
                                            <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                                max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                            <span>～</span>
                                            <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                                max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                        </div>
                                        <div class="period-select-buttons">
                                            @if ($search_period < 1)
                                                <button type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>"  onclick="changeXlength(this, '3')">3時間</button>
                                                <button type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'6')">6時間</button>
                                                <button type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'12')">12時間</button>
                                                <button type="button" class="<?php echo $time_period == '24' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'24')">24時間</button>
                                            @elseif ($search_period < 7)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 30)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 180)
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @else
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @endif
                                        </div>

                                        <div class="graph-area">
                                            <input type="hidden" class="min-time" value="{{date('Y-m-d H:i:s', strtotime($item->starttime))}}"/>
                                            <a class="prev" onclick="moveXRange(this, -1)">❮</a>
                                            <a class="next" onclick="moveXRange(this, 1)">❯</a>
                                            <canvas onclick="location.href='{{route('admin.shelf.past_analysis')}}'+'?change_params=change&starttime=' + '{{date('Y-m-d', strtotime($item->starttime))}}'
                                                + '&endtime='+'{{date('Y-m-d', strtotime($item->endtime))}}'+'&selected_rule='+{{$selected_rule}}+'&time_period='+'{{$time_period}}'"
                                                id="past_graph_shelf" class="graph-canvas"></canvas>
                                            <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                            <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                            <input type="hidden" class="search_period" value="{{$search_period}}"/>
                                        </div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['recent_detect_thief'])
                                    @if(isset($item->thief_detection))
                                        <?php
                                            $video_path = '';
                                            $video_path .= asset('storage/video/').'/';
                                            $video_path .= $item->thief_detection['video_file_path'];

                                            $thumb_path = asset('storage/thumb/').'/'.$item->thief_detection['thumb_img_path'];
                                        ?>
                                        <div class="camera-id">カメラID：{{$item->thief_detection['serial_no']}}</div>
                                        <div class="movie" video-path = "{{$video_path}}">
                                            <a data-target="movie0000"
                                                {{-- onclick="videoPlay(this, '{{$video_path}}')"  --}}
                                                class="video-open setting2 play">
                                                <img src="{{$thumb_path}}"/>
                                            </a>
                                        </div>
                                        <video style="" class = 'video-play' src = '{{$video_path}}' type= 'video/mp4' controls></video>
                                        <div class="cap">検知時間：<time>{{date('Y/m/d H:i', strtotime($item->thief_detection['starttime']))}}</time></div>
                                        <div class="cap">ルール：<time>{{isset($item->thief_detection['rule_name']) ? $item->thief_detection['rule_name'] : ''}}</time></div>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['detect_list_thief'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                        <div class="search-period-area">
                                            <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                                max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                            <span>～</span>
                                            <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                                max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                        </div>
                                    @endif
                                    @if (isset($item->thief_detections) && count($item->thief_detections) > 0)
                                        <video style="" class = 'video-play list-video' src = '' type= 'video/mp4' controls></video>
                                        <p class="video-notice detect-content none"></p>
                                        <p class="video-notice none">動画の30秒あたりが検知のタイミングになります。</p>
                                        <table class="thief-detect-list list-table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>カメラNo</th>
                                                    <th class="time">時間</th>
                                                    <th class="">設置エリア</th>
                                                    <th class="">設置場所</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($item->thief_detections as $detection_item)
                                                <?php
                                                    $detection_item = (array)$detection_item;
                                                    $video_path = '';
                                                    $video_path .= asset('storage/video/').'/';
                                                    $video_path .= $detection_item['video_file_path'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div video-path = "{{$video_path}}">
                                                            <a data-target="movie0000" class="video-open list-play-icon play"></a>
                                                        </div>
                                                    </td>
                                                    <td>{{$detection_item['serial_no']}}</td>
                                                    <td>{{date('Y/m/d H:i', strtotime($detection_item['starttime']))}}</td>
                                                    <td>{{$detection_item['location_name']}}</td>
                                                    <td>{{$detection_item['installation_position']}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['live_graph_thief'])
                                    @if(isset($item->selected_camera))
                                        <div class="camera-id">カメラID：{{$item->selected_camera['serial_no']}}</div>
                                    @endif
                                    <div class="period-select-buttons">
                                        <?php
                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                        ?>
                                        <button onclick="changeXlength(this, '3')" type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>">3時間</button>
                                        <button onclick="changeXlength(this, '6')" type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>">6時間</button>
                                        <button onclick="changeXlength(this, '12')" type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>">12時間</button>
                                    </div>
                                    <div class="graph-area">
                                        <canvas onclick="location.href='{{route('admin.thief.detail')}}'" id="live_graph_thief" class="graph-canvas"></canvas>
                                        <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                        <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                    </div>
                                @elseif($item->block_type == config('const.top_block_type_codes')['past_graph_thief'])
                                    @if (isset($item->starttime) && $item->starttime != '' && isset($item->endtime) && $item->endtime != '')
                                        <?php
                                            $selected_rule = '';
                                            if (isset($item->selected_rule) && $item->selected_rule > 0){
                                                $selected_rule = $item->selected_rule;
                                            }
                                            $selected_rule_object = null;
                                            if ($selected_rule > 0){
                                                foreach($item->rules as $rule_item){
                                                    if ($rule_item->id == $selected_rule){
                                                        $selected_rule_object = $rule_item;
                                                    }
                                                }
                                            }

                                            $time_period = '3';
                                            if (isset($item->time_period) && $item->time_period != '') $time_period = $item->time_period;
                                            $starttime = date('Y-m-d', strtotime($item->starttime));
                                            $endtime = date('Y-m-d', strtotime($item->endtime));
                                            $search_period = (strtotime($endtime) - strtotime($starttime))/86400;
                                            if ($search_period < 1) {
                                                if (!in_array($time_period, ['3', '6', '12', '24'])){
                                                    $time_period = '3';
                                                }
                                            } else if ($search_period < 7 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 30 ) {
                                                if (!in_array($time_period, ['time', 'day',])){
                                                    $time_period = 'time';
                                                }
                                            } else if ($search_period <= 180 ) {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            } else {
                                                if (!in_array($time_period, ['day', 'week','month'])){
                                                    $time_period = 'day';
                                                }
                                            }
                                        ?>

                                        <div class="camera-id">{{$selected_rule_object != null ? $selected_rule_object->name.'('.$selected_rule_object->serial_no. ')' : ''}}</div>
                                        <div class="search-period-area">
                                            <input class="starttime" onchange="changePeriod(this, 'starttime', {{$item->id}})"
                                                max="{{strtotime($item->endtime) > strtotime(date('Y-m-d')) ? date('Y-m-d') : date('Y-m-d', strtotime($item->endtime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->starttime))}}"/>
                                            <span>～</span>
                                            <input class="endtime" onchange="changePeriod(this, 'endtime', {{$item->id}})"
                                                max="{{date('Y-m-d')}}" min="{{date('Y-m-d', strtotime($item->starttime))}}"
                                                type="date" value="{{date('Y-m-d', strtotime($item->endtime))}}"/>
                                        </div>
                                        <div class="period-select-buttons">
                                            @if ($search_period < 1)
                                                <button type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>"  onclick="changeXlength(this, '3')">3時間</button>
                                                <button type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'6')">6時間</button>
                                                <button type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'12')">12時間</button>
                                                <button type="button" class="<?php echo $time_period == '24' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this,'24')">24時間</button>
                                            @elseif ($search_period < 7)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 30)
                                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'time')">時間別</button>
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                            @elseif ($search_period <= 180)
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @else
                                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'day')">日別</button>
                                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'week')">週別</button>
                                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'?>" onclick="changeXlength(this, 'month')">月別</button>
                                            @endif
                                        </div>

                                        <div class="graph-area">
                                            <input type="hidden" class="min-time" value="{{date('Y-m-d H:i:s', strtotime($item->starttime))}}"/>
                                            <a class="prev" onclick="moveXRange(this, -1)">❮</a>
                                            <a class="next" onclick="moveXRange(this, 1)">❯</a>
                                            <canvas onclick="location.href='{{route('admin.thief.past_analysis')}}'+'?change_params=change&starttime=' + '{{date('Y-m-d', strtotime($item->starttime))}}'
                                                + '&endtime='+'{{date('Y-m-d', strtotime($item->endtime))}}'+'&selected_rule='+{{$selected_rule}}+'&time_period='+'{{$time_period}}'"
                                                id="past_graph_thief" class="graph-canvas"></canvas>
                                            <input type="hidden" class="block-data" value="{{json_encode($item)}}"/>
                                            <input type="hidden" class="time_period" value="{{$time_period}}"/>
                                            <input type="hidden" class="search_period" value="{{$search_period}}"/>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
            </div>
            <div class="no-data top-no-data" style="font-size: 16px;display:<?php echo count($top_blocks) == 0 ? 'block' : 'none' ?>">
                表示項目を追加することでデータを表示できます。
            </div>
        </div>
	</div>
</div>

<form action="{{route('admin.top')}}" method="get" name="form" id="top_form">
@csrf
<input name="scroll_top" id="scroll_top" type="hidden" value="{{isset($scroll_top) ? $scroll_top : ''}}"/>
<input name="selected_top_block" id = 'selected_top_block' type="hidden"/>

<!--MODAL -->
<div id="movie0000" class="modal-content">
    <div class="textarea">
        <div class="v">
            <video id = 'video-container' src = '' type= 'video/mp4' controls>
            </video>
            <p class="video-notice">動画の30秒あたりが検知のタイミングになります。</p>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->

<!--MODAL -->
<div id="camera" class="modal-content">
    <div class="textarea">
        <div class="listing">
            <input id = 'selected_camera' type="hidden"/>
            <div class="scroll active sp-pl0">
                <table class="table2 text-centre">
                    <thead>
                    <tr>
                        <th class="w10"></th>
                        <th>カメラNo</th>
                        <th>設置エリア</th>
                        <th>設置場所</th>
                        <th>カメラ画像確認</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="modal-set">
                    <button onclick="changeSelectCamera()" type="button" class="modal-close">設 定</button>
                </div>
            </div>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->

<!--MODAL -->
<div id="rule" class="modal-content">
    <div class="textarea">
        <div class="listing">
            <input id = 'selected_rule' type="hidden"/>
            <div class="scroll active sp-pl0">
                <table class="table2 text-centre">
                </table>
                <div class="modal-set">
                    <button onclick="changeSelectRule()" type="button" class="modal-close">設 定</button>
                </div>
            </div>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->

<!--MODAL -->
<div id="extension-modal" class="modal-content">
    <div class="extentsion-content">
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
</form>
<div id="dialog-confirm" title="test" style="display:none">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
    <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<script src="{{ asset('assets/admin/js/gridstack-all.js?2') }}"></script>
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}" defer></script>
<script src="{{ asset('assets/admin/js/helper.js?25') }}" defer></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>
<script src="{{ asset('assets/admin/js/konva.js?2') }}" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js" defer></script>

<script defer>
    var options = { // put in gridstack options here
        disableResize:false,
        disableDrag:false,
        float: false
    };
    var color_set = {
        1:'red',
        2:'#42b688',
        3:'#42539a',
        4:'black',
    }
    var myLineChart = {};
    var actions = <?php echo json_encode(config('const.action'));?>;
    var stages = {};
    var layers = {};


    function videoPlay(e, path){
        var video = document.getElementById('video-container');
        video.pause();
        allVideoStop();
        $('#video-container').attr('src', path);
        video.play();

        $('html, body').addClass('lock');
        $('body').append('<div class="modal-overlay"></div>');
        $('.modal-overlay').fadeIn('fast');

        var modal = '#' + $(e).attr('data-target');
        $(modal).wrap("<div class='modal-wrap'></div>");
        $('.modal-wrap').fadeIn();
        modalResize();
        $(modal).fadeIn('fast');
        $('.textarea').click(function (e) {
            e.stopPropagation();
        });
        $('.modal-wrap, .modal-close, .ok').off().click(function () {
            if($('.video-open').hasClass('play')){
                allVideoStop();
                if (video != undefined){
                    video.pause();
                }
            }
            $(modal).fadeOut('fast');
            $('.modal-overlay').fadeOut('fast', function () {
                $('html, body').removeClass('lock');
                $('.modal-overlay').remove();
                $(modal).unwrap("<div class='modal-wrap'></div>");
            });
        })
        $(window).on('resize', function () {
            modalResize();
        });
        function modalResize() {
            // ウィンドウの横幅、高さを取得
            var w = $(window).width();
            var h = $(window).height();
            // モーダルコンテンツの横幅、高さを取得
            var mw = $(modal).outerWidth(true);
            var mh = $(modal).outerHeight(true);
            // モーダルコンテンツの表示位置を設定
            if ((mh > h) && (mw > w)) {
                $(modal).css({
                'left': 0 + 'px',
                'top': 0 + 'px'
                });
            } else if ((mh > h) && (mw < w)) {
                var x = (w - scrollsize - mw) / 2;
                $(modal).css({
                'left': x + 'px',
                'top': 0 + 'px'
                });
            } else if ((mh < h) && (mw > w)) {
                var y = (h - scrollsize - mh) / 2;
                $(modal).css({
                'left': 0 + 'px',
                'top': y + 'px'
                });
            } else {
                var x = (w - mw) / 2;
                var y = (h - mh) / 2;
                $(modal).css({
                'left': x + 'px',
                'top': y + 'px'
                });
            }
        }

    }

    function showEditMenu(e){
        $(e).toggleClass("on");
        $('.user-menu').fadeToggle(5);
        if ($(e).hasClass('on')){
            $('.total-menu-letter').html('閉じる');
        } else {
            $('.total-menu-letter').html('表示項目を追加');
        }
    }

    function gearClick(block_item, action){
        $('[data-menu-id="'+ block_item.id + '"]').hide();
        $('.close-gear-icon').hide();
        $('svg', $('.gear-block')).show();
        $('#selected_rule').val('');
        var recent_img_path = '<?php echo asset("storage/recent_camera_image/") ;?>';
        var img_url = '';
        switch(action){
            case 'delete':
                delete_id = block_item.id;
                helper_confirm("dialog-confirm", "削除", "削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
                    $('[data-id="'+ block_item.id + '"]').remove();
                    if ($('.grid-stack-item').length == 0){
                        $('.grid-stack').height('auto');
                        $('.top-no-data').show();
                    }
                    deleteTopBlock(block_item.id);
                });
                break;
            case 'change_camera':
                if (block_item.cameras != undefined && block_item.cameras.length > 0){
                    $('html, body').addClass('lock');
                    $('body').append('<div class="modal-overlay"></div>');
                    $('.modal-overlay').fadeIn('fast');
                    $('#camera').wrap("<div class='modal-wrap'></div>");
                    $('.modal-wrap').fadeIn();
                    $('#camera').show();

                    $('#camera').fadeIn('fast');
                    $('.textarea').click(function (e) {
                        e.stopPropagation();
                    });
                    $('.modal-wrap, .modal-close, .ok').off().click(function () {
                        $('#camera').fadeOut('fast');
                        $('.modal-overlay').fadeOut('fast', function () {
                            $('html, body').removeClass('lock');
                            $('.modal-overlay').remove();
                            $('#camera').unwrap("<div class='modal-wrap'></div>");
                        });
                        $('tbody > tr', $('#camera')).remove();
                    });

                    $('#selected_top_block').val(block_item.id);
                    block_item.cameras.map(camera => {
                        var checked = '';
                        if (block_item.selected_camera != undefined && block_item.selected_camera != null && camera.id == block_item.selected_camera.id) checked = 'checked';
                        var tr_record = '<tr>';
                        tr_record += '<td class="stick-t">';
                        tr_record += '<div class="checkbtn-wrap radio-wrap-div">';
                        tr_record += '<input class="selected_camera" name="selected_camera" value = "' + camera.id + '" type="radio" id="camera' + camera.id + '"' + checked + '/>';
                        tr_record += '<label for="camera' + camera.id + '"></label>';
                        tr_record += '</div>';
                        tr_record += '</td>';

                        tr_record += '<td>' + camera.serial_no + '</td>';
                        tr_record += '<td>' + camera.location_name + '</td>';
                        tr_record += '<td>' + camera.installation_position + '</td>';
                        img_url = recent_img_path + '/' + camera.camera_id + '.jpeg';
                        if (camera.is_on == true){
                            tr_record += '<td><img width="100px" src="' + img_url + '"/></td>';
                        } else {
                            tr_record += '<td>カメラ停止中</td>';
                        }
                        tr_record += '</tr>';
                        $('tbody', $('#camera')).append(tr_record);
                        $('.selected_camera').click(function(){
                            var selected_camera_id = $(this).attr('id');
                            selected_camera_id = selected_camera_id.replace('camera', '');
                            $('#selected_camera').val(selected_camera_id);
                        })
                    })
                }
                break;
            case 'change_rule':
                if (block_item.rules != undefined && block_item.rules.length > 0){
                    $('html, body').addClass('lock');
                    $('body').append('<div class="modal-overlay"></div>');
                    $('.modal-overlay').fadeIn('fast');
                    $('#rule').wrap("<div class='modal-wrap'></div>");
                    $('.modal-wrap').fadeIn();
                    $('#rule').show();

                    $('#rule').fadeIn('fast');
                    $('.textarea').click(function (e) {
                        e.stopPropagation();
                    });
                    $('.modal-wrap, .modal-close, .ok').off().click(function () {
                        $('#rule').fadeOut('fast');
                        $('.modal-overlay').fadeOut('fast', function () {
                            $('html, body').removeClass('lock');
                            $('.modal-overlay').remove();
                            $('#rule').unwrap("<div class='modal-wrap'></div>");
                        });
                        $('table', $('#rule')).empty();
                    });

                    $('#selected_top_block').val(block_item.id);
                    var table_content = '';
                    switch (block_item.block_type){
                        case parseInt("<?php echo config('const.top_block_type_codes')['detect_list_pit'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rules != undefined && block_item.selected_rules != null && block_item.selected_rules.includes(rule.id.toString())){
                                    checked = 'checked';
                                }
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="rule_checkbox" value = "' + rule.id + '" type="checkbox" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label class="custom-style" for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';
                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                        case parseInt("<?php echo config('const.top_block_type_codes')['detect_list_danger'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>アクション</th><th>カラー</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rules != undefined && block_item.selected_rules != null && block_item.selected_rules.includes(rule.id.toString())){
                                    checked = 'checked';
                                }
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="rule_checkbox" value = "' + rule.id + '" type="checkbox" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label class="custom-style" for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';

                                table_content += '<td>'
                                var action_ids = rule.action_id;
                                action_ids = JSON.parse(action_ids);
                                action_ids.map(action_id => {
                                    table_content += '<div>' + actions[action_id] + '</div>';
                                })
                                table_content += '</td>';
                                table_content += '<td><input disabled type="color" value ="' + rule.color + '"</td>';

                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                        case parseInt("<?php echo config('const.top_block_type_codes')['past_graph_pit'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rule > 0 && rule.id == block_item.selected_rule) checked = 'checked';
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="selected_rule" name="selected_rule" value = "' + rule.id + '" type="radio" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';
                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                        case parseInt("<?php echo config('const.top_block_type_codes')['past_graph_danger'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>アクション</th><th>カラー</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rule > 0 && rule.id == block_item.selected_rule) checked = 'checked';
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="selected_rule" name="selected_rule" value = "' + rule.id + '" type="radio" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';

                                table_content += '<td>'
                                var action_ids = rule.action_id;
                                action_ids = JSON.parse(action_ids);
                                action_ids.map(action_id => {
                                    table_content += '<div>' + actions[action_id] + '</div>';
                                })
                                table_content += '</td>';
                                table_content += '<td><input disabled type="color" value ="' + rule.color + '"</td>';
                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                        case parseInt("<?php echo config('const.top_block_type_codes')['detect_list_shelf'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>カラー</th><th>定時撮影時刻</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rules != undefined && block_item.selected_rules != null && block_item.selected_rules.includes(rule.id.toString())){
                                    checked = 'checked';
                                }
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="rule_checkbox" value = "' + rule.id + '" type="checkbox" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label class="custom-style" for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';

                                table_content += '<td><input disabled type="color" value ="' + rule.color + '"</td>';
                                table_content += '<td>' + rule.hour + ':' + (rule.mins < 10 ? ('0' + rule.mins) : rule.mins) + '</td>';
                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                        case parseInt("<?php echo config('const.top_block_type_codes')['past_graph_shelf'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>カラー</th><th>定時撮影時刻</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rule > 0 && rule.id == block_item.selected_rule) checked = 'checked';
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="selected_rule" name="selected_rule" value = "' + rule.id + '" type="radio" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';
                                table_content += '<td><input disabled type="color" value ="' + rule.color + '"</td>';
                                table_content += '<td>' + rule.hour + ':' + (rule.mins < 10 ? ('0' + rule.mins) : rule.mins) + '</td>';
                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                        case parseInt("<?php echo config('const.top_block_type_codes')['detect_list_thief'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>ハンガー</th><th>カラー</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rules != undefined && block_item.selected_rules != null && block_item.selected_rules.includes(rule.id.toString())){
                                    checked = 'checked';
                                }
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="rule_checkbox" value = "' + rule.id + '" type="checkbox" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label class="custom-style" for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';
                                table_content += '<td><input disabled type="color" value ="' + rule.hanger + '"</td>';
                                table_content += '<td><input disabled type="color" value ="' + rule.color + '"</td>';
                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                        case parseInt("<?php echo config('const.top_block_type_codes')['past_graph_thief'];?>"):
                            table_content += '<thead><tr>';
                            table_content += '<th></th><th>ルール名</th><th>カメラNo</th><th>設置エリア</th><th>設置場所</th><th>ハンガー</th><th>カラー</th><th>ルールの設定期間</th><th>カメラ画像確認</th>';
                            table_content += '</tr></thead>';
                            table_content += '<tbody>';
                            block_item.rules.map(rule => {
                                var checked = '';
                                if (block_item.selected_rule > 0 && rule.id == block_item.selected_rule) checked = 'checked';
                                table_content += '<tr>';
                                table_content += '<td class="stick-t">';
                                table_content += '<div class="checkbtn-wrap radio-wrap-div">';
                                table_content += '<input class="selected_rule" name="selected_rule" value = "' + rule.id + '" type="radio" id="rule' + rule.id + '"' + checked + '/>';
                                table_content += '<label for="rule' + rule.id + '"></label>';
                                table_content += '</div>';
                                table_content += '</td>';

                                table_content += '<td>' + (rule.name != null ? rule.name : '') + '</td>';
                                table_content += '<td>' + rule.serial_no + '</td>';
                                table_content += '<td>' + rule.location_name + '</td>';
                                table_content += '<td>' + rule.installation_position + '</td>';
                                table_content += '<td><input disabled type="color" value ="' + rule.hanger + '"</td>';
                                table_content += '<td><input disabled type="color" value ="' + rule.color + '"</td>';
                                table_content += '<td>' + formatDateLine(rule.created_at) + '～' + (rule.deleted_at != null ? formatDateLine(rule.deleted_at) :'') + '</td>';
                                img_url = recent_img_path + '/' + rule.device_id + '.jpeg';
                                if (rule.is_on == true){
                                    table_content += '<td><img width="100px" src="' + img_url + '"/></td>';
                                } else {
                                    table_content += '<td>カメラ停止中</td>';
                                }
                                table_content += '</tr>';
                            })
                            table_content += '</tbody>';
                            break;
                    }
                    $('table', $('#rule')).append(table_content);
                    $('.selected_rule').click(function(){
                        var selected_rule_id = $(this).attr('id');
                        selected_rule_id = selected_rule_id.replace('rule', '');
                        $('#selected_rule').val(selected_rule_id);
                    })
                }
                break;
            case 'change_x_axis':
                break;
        }
    }
    function showExtensionModal(e, title){
        $('html, body').addClass('lock');
        $('body').append('<div class="modal-overlay"></div>');
        $('.modal-overlay').fadeIn('fast');
        $('#extension-modal').wrap("<div class='modal-wrap'></div>");
        $('.modal-wrap').fadeIn();
        $('#extension-modal').show();
        $('#extension-modal').fadeIn('fast');
        $('.extentsion-content').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            if ($('.video-play', $('#extension-modal')).length > 0){
                $('.video-play', $('#extension-modal')).hide();
                // $('.video-play', $('#extension-modal'))[0].pause();
                allVideoStop();
                if($('.list-table', $('#extension-modal')).length > 0){
                    $('.list-table', $('#extension-modal')).show();
                }
                $('.video-notice', $('#extension-modal')).hide();
            }
        });

        var conent_node = $('.grid-contents', $(e).parent().parent());
        $('.extentsion-content', $('#extension-modal')).append('<div class="modal-title">' + title + '</div>')
        $('.extentsion-content', $('#extension-modal')).append(conent_node.clone());

        $('.image-container', conent_node).attr('id', '');
        $('.streaming-video', conent_node).attr('id', '');
        $('.graph-canvas', conent_node).attr('id', '');
        drawRules();
        refreshGraph();
        load();
        $('.video-open', $('#extension-modal')).unbind();
        $('.video-open', $('#extension-modal')).click(function (e) {
            var video_path = $(this).parent().attr('video-path');
            e.stopPropagation();
            e.preventDefault();
            if($(this).hasClass('play')){
                allVideoStop();
                $('.video-play', $('#extension-modal')).attr('src', video_path);
                $('.video-play', $('#extension-modal')).show();
                $('.video-play', $('#extension-modal'))[0].play();
                $('.video-notice', $('#extension-modal')).show();
                if ($('.list-table', $('#extension-modal')).length > 0){
                    $('.list-table', $('#extension-modal')).hide();
                }
            }
        })
        //-------------------------------

        $('.modal-wrap,.modal-close, .ok').off().click(function () {
            $('#extension-modal').fadeOut('fast');
            $('.modal-overlay').fadeOut('fast', function () {
                $('html, body').removeClass('lock');
                $('.modal-overlay').remove();
                $('#extension-modal').unwrap("<div class='modal-wrap'></div>");
                var image_container_id = '';
                var image_containter_element = $('.image-container', $('.extentsion-content', $('#extension-modal')));
                if (image_containter_element.length > 0){
                    image_container_id = image_containter_element.attr('id');
                }

                var graph_canvas_id = '';
                var graph_canvas_element = $('.graph-canvas', $('.extentsion-content', $('#extension-modal')));
                if (graph_canvas_element.length > 0){
                    graph_canvas_id = graph_canvas_element.attr('id');
                }
                $('.extentsion-content', $('#extension-modal')).empty();
                if (graph_canvas_id != ''){
                    $('.graph-canvas').each(function(){
                        if ($(this).attr('id') == '') $(this).attr('id', graph_canvas_id);
                    })
                }
                if (image_container_id != ''){
                    $('.image-container').each(function(){
                        if ($(this).attr('id') == '') $(this).attr('id', image_container_id);
                    })
                    $('.streaming-video').each(function(){
                        if ($(this).attr('id') == '') $(this).attr('id', 'streaming_video_' + image_container_id.replace('image_container_', ''));
                    })
                }
                drawRules();
                load();
                refreshGraph();
            });
        });
    }

    function toggleMenu(e, id){
        $(e).toggleClass("on");
        $('.gear-menu').hide();
        $('.close-gear-icon').hide();
        $('.gear-button').show();
        if ($(e).hasClass('on')){
            $('svg', $(e)).hide();
            $('.close-gear-icon', $(e)).show();
            $('[data-menu-id="'+ id + '"]').show();
        } else {
            $('svg', $(e)).show();
            $('.close-gear-icon', $(e)).hide();
            $('[data-menu-id="'+ id + '"]').hide();
        }
    }

    function load() {
        safieStreamingPlayerElements = $('safie-streaming-player');
        safieStreamingPlayerElements.each(function(index){
            safieStreamingPlayerElement = safieStreamingPlayerElements[index];
            var camera_id = $(safieStreamingPlayerElement).attr('data-camera-id');
            var access_token = $(safieStreamingPlayerElement).attr('data-token');
            if(safieStreamingPlayerElement != undefined && safieStreamingPlayerElement != null){
                safieStreamingPlayer = safieStreamingPlayerElement.instance;
                safieStreamingPlayer.on('error', (error) => {
                    console.error(error);
                });
                // 初期化
                safieStreamingPlayer.defaultProperties = {
                    defaultAccessToken: access_token,
                    defaultDeviceId: camera_id,
                    defaultAutoPlay:true,
                    defaultUserInteractions:false
                };
            }
        })

    }
    function resortDangerData(data, x_range){
        var temp = {};
        switch(x_range){
            case 'day':
                Object.keys(data).map(date_time => {
                    var date = formatDateLine(date_time);
                    if (temp[date] == undefined) temp[date] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date][id] == undefined) temp[date][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date][action_id] += data[date_time][action_id].length;
                    })
                })
                break;
            case 'week':
                Object.keys(data).map(date_time => {
                    var date = formatYearWeekNum(date_time);
                    if (temp[date] == undefined) temp[date] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date][id] == undefined) temp[date][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date][action_id] += data[date_time][action_id].length;
                    })
                })
                break;
            case 'month':
                Object.keys(data).map(date_time => {
                    var date = formatYearMonth(date_time);
                    if (temp[date] == undefined) temp[date] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date][id] == undefined) temp[date][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date][action_id] += data[date_time][action_id].length;
                    })
                })
                break;
            default:
                Object.keys(data).map(date_time => {
                    if (temp[date_time] == undefined) temp[date_time] = {};
                    Object.keys(actions).map(id => {
                        if (temp[date_time][id] == undefined) temp[date_time][id] = 0;
                    })
                    Object.keys(data[date_time]).map(action_id => {
                        temp[date_time][action_id] += data[date_time][action_id].length;
                    })
                })
        }
        return temp;
    }

    function resortPitData(data, time_period){
        var temp = {};
        switch(time_period){
            case 'day':
                Object.keys(data).map(date_time => {
                    var date = formatDateLine(date_time);
                    temp[date] = data[date_time];
                })
                break;
            case 'week':
                Object.keys(data).map(date_time => {
                    var date = formatYearWeekNum(date_time);
                    temp[date] = data[date_time];
                })
                break;
            case 'month':
                Object.keys(data).map(date_time => {
                    var date = formatYearMonth(date_time);
                    temp[date] = data[date_time];
                })
                break;
            default:
                temp = data;
        }
        return temp;
    }

    function resortGraphData(data, time_period){
        var temp = {};
        switch(time_period){
            case 'day':
                Object.keys(data).map(date_time => {
                    var date = formatDateLine(date_time);
                    if (temp[date] == undefined) temp[date] = 0;
                    temp[date] += data[date_time];
                })
                break;
            case 'week':
                Object.keys(data).map(date_time => {
                    var date = formatYearWeekNum(date_time);
                    if (temp[date] == undefined) temp[date] = 0;
                    temp[date] += data[date_time];
                })
                break;
            case 'month':
                Object.keys(data).map(date_time => {
                    var date = formatYearMonth(date_time);
                    if (temp[date] == undefined) temp[date] = 0;
                    temp[date] += data[date_time];
                })
                break;
            default:
                return data;
        }
        return temp;
    }

    function drawPitGraph(ctx, block_data, x_range, min_x_time = ''){
        var graph_id = $(ctx).attr('id');
        var grid_unit = 60;
        var x_unit = 'minute';
        var x_display_format = {'minute': 'H:mm'};
        var tooltipFormat = 'H:mm';
        var now = new Date();
        var min_time = new Date();
        var max_time = new Date();
        var graph_data = null;
        switch(graph_id){
            case 'live_graph_pit':
                graph_data = block_data.pit_live_graph_data != undefined ? block_data.pit_live_graph_data : {};
                x_range = parseInt(x_range);
                max_time.setHours(now.getHours() + 1);
                max_time.setMinutes(0);
                max_time.setSeconds(0);
                min_time.setHours((now.getHours() - (x_range -1 )) < 0 ? 0 : now.getHours() -(x_range -1 ));
                min_time.setMinutes(0);
                min_time.setSeconds(0);
                if (x_range == 6) grid_unit = 30;
                if (x_range == 12 || x_range == 24) grid_unit = 60;
                break;
            case 'past_graph_pit':
                graph_data = block_data.pit_past_graph_data != undefined ? block_data.pit_past_graph_data : {};
                min_time = min_time != '' ? new Date(min_x_time): new Date();
                max_time = new Date(min_time);
                if (!isNaN(parseInt(x_range))) x_range = parseInt(x_range);
                var endtime = new Date(block_data.endtime);
                endtime.setHours(23);
                endtime.setMinutes(59);
                endtime.setSeconds(59);
                switch(x_range){
                    case 3:
                        grid_unit = 15;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 6:
                        grid_unit = 30;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 12:
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 24:
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 'time':
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'DD日H時'};
                        tooltipFormat = "MM/DD H:mm";
                        max_time.setDate(max_time.getDate() + 1);
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 'day':
                        grid_unit = 1;
                        x_unit = 'day';
                        x_display_format = {'day': 'M/DD'};
                        tooltipFormat = "YY/MM/DD";
                        max_time.setDate(max_time.getDate() + 7);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                    case 'week':
                        grid_unit = 1;
                        x_unit = 'week';
                        x_display_format = {'week': 'M/DD'};
                        tooltipFormat = "YY/MM/DD";
                        max_time.setDate(max_time.getDate() + 28);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                    case 'month':
                        grid_unit = 1;
                        x_unit = 'month';
                        x_display_format = {'month': 'YYYY/MM'};
                        tooltipFormat = "YY/MM";
                        max_time.setMonth(max_time.getMonth() + 6);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                }
                if (max_time.getTime() > endtime.getTime()) max_time = new Date(endtime);

                break;
        }
        graph_data = resortPitData(graph_data, x_range);
        var cur_time = new Date(min_time);
        cur_time.setHours(min_time.getHours());
        cur_time.setMinutes(0);
        cur_time.setSeconds(0);

        var time_labels = [];
        var y_data = [];
        var point_radius = [];

        while(cur_time.getTime() <= max_time.getTime()){
            time_labels.push(new Date(cur_time));
            point_radius.push(0);
            if (y_data.length > 0){
                y_data.push(y_data[y_data.length - 1]);
            } else {
                y_data.push(null);
            }

            if (x_range == 'day' || x_range == 'week' || x_range == 'month'){
                var date_key = formatDateLine(cur_time);
                if (x_range == 'week') date_key = formatYearWeekNum(cur_time);
                if (x_range == 'month') date_key = formatYearMonth(cur_time);
                if (graph_data[date_key] != undefined){
                    y_data[y_data.length - 1] = graph_data[date_key];
                    point_radius[point_radius.length - 1] = 3;
                }
            } else {
                Object.keys(graph_data).map((time, index) => {
                    if (new Date(time).getTime() >= cur_time.getTime() && new Date(time).getTime() < cur_time.getTime() + grid_unit* 60 * 1000 && new Date(time).getTime() <= max_time.getTime()){
                        if (index == 0){
                            if (new Date(time).getTime() != cur_time.getTime()) {
                                time_labels.push(new Date(time));
                                point_radius.push(0);
                                if (y_data.length > 0){
                                    y_data.push(y_data[y_data.length - 1]);
                                } else {
                                    y_data.push(null);
                                }
                            }
                        } else {
                            time_labels.push(new Date(time));
                            y_data.push(graph_data[time]);
                            point_radius.push(3);
                        }
                        point_radius[point_radius.length - 1] = 3;
                        y_data[y_data.length - 1] = graph_data[time];
                    }
                })
            }

            switch(x_range){
                case 'time':
                    cur_time.setHours(cur_time.getHours() + 1);
                    break;
                case 'day':
                    cur_time.setDate(cur_time.getDate() + 1);
                    break;
                case 'week':
                    cur_time.setDate(cur_time.getDate() + 7);
                    break;
                case 'month':
                    cur_time.setMonth(cur_time.getMonth() + 1);
                    break;
                default:
                    cur_time.setMinutes(cur_time.getMinutes() + grid_unit);
                    break;
            }
        }
        ctx.innerHTML = '';
        if (myLineChart[block_data.id] != undefined){
            myLineChart[block_data.id].destroy();
        }
        myLineChart[block_data.id] = new Chart(ctx, {
            type: 'line',
            data: {
                labels:time_labels,
                datasets: [{
                    label: '人',
                    steppedLine:'before',
                    data: y_data,
                    borderColor: "#42b688",
                    backgroundColor: "rgba(66,182,136, 0.3)",
                    pointBackgroundColor:'red',
                    radius:point_radius,
                    fill:true
                }],
                mousemove: function(){
                    return;
                },
            },
            options: {
                legend: {
                    labels: {
                        fontSize: 12
                    }
                },
                title: {
                    display: false,
                    text: 'ピット内人数推移'
                },
                responsive: true,
                interaction: {
                    intersect: false,
                    axis: 'x'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            suggestedMax: Math.max(...y_data) + 1,
                            suggestedMin: 0,
                            stepSize: parseInt((Math.max(...y_data) + 2)/5) + 1,
                            fontSize: 12,
                            callback: function(value, index, values){
                                return  value +  '人'
                            }
                        }
                    }],
                    xAxes:[{
                        type: 'time',
                        time: {
                            unit: x_unit,
                            displayFormats: x_display_format,
                            tooltipFormat:tooltipFormat,
                            distribution: 'series',
                            stepSize: grid_unit,
                            format:'HH:mm'
                        },
                        ticks: {
                            fontSize: 12,
                            // max: max_time,
                            // min: min_time,
                        }
                    }]
                },

            }
        });
    }

    function drawDangerGraph(ctx, block_data, x_range, min_x_time = ''){
        var graph_id = $(ctx).attr('id');
        var graph_data = null;
        var grid_unit = 15;
        var x_unit = 'minute';
        var x_display_format = {'minute': 'H:mm'};
        var tooltipFormat = 'H:mm';
        var now = new Date();
        var min_time = new Date();
        var max_time = new Date();
        switch(graph_id){
            case 'live_graph_danger':
                graph_data = block_data.danger_live_graph_data != undefined ? block_data.danger_live_graph_data : {};
                max_time.setHours(now.getHours() + 1);
                max_time.setMinutes(0);
                max_time.setSeconds(0);

                x_range = parseInt(x_range);
                min_time.setHours((now.getHours() - (x_range -1 )) < 0 ? 0 : now.getHours() -(x_range -1 ));
                min_time.setMinutes(0);
                min_time.setSeconds(0);
                graph_data = resortDangerData(graph_data, x_range);
                if (x_range == 6) grid_unit = 30;
                if (x_range == 12 || x_range == 24) grid_unit = 60;
                break;
            case 'past_graph_danger':
                graph_data = block_data.danger_past_graph_data != undefined ? block_data.danger_past_graph_data : {};
                min_time = min_time != '' ? new Date(min_x_time): new Date();
                max_time = new Date(min_time);
                if (!isNaN(parseInt(x_range))) x_range = parseInt(x_range);
                var endtime = new Date(block_data.endtime);
                endtime.setHours(23);
                endtime.setMinutes(59);
                endtime.setSeconds(59);
                switch(x_range){
                    case 3:
                        grid_unit = 15;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 6:
                        grid_unit = 30;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 12:
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 24:
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 'time':
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'DD日H時'};
                        tooltipFormat = "MM/DD H:mm";
                        max_time.setDate(max_time.getDate() + 1);
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 'day':
                        grid_unit = 1;
                        x_unit = 'day';
                        x_display_format = {'day': 'M/DD'};
                        tooltipFormat = "YY/MM/DD";
                        max_time.setDate(max_time.getDate() + 7);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                    case 'week':
                        grid_unit = 1;
                        x_unit = 'week';
                        x_display_format = {'week': 'M/DD'};
                        tooltipFormat = "YY/MM/DD";
                        max_time.setDate(max_time.getDate() + 28);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                    case 'month':
                        grid_unit = 1;
                        x_unit = 'month';
                        x_display_format = {'month': 'YYYY/MM'};
                        tooltipFormat = "YY/MM";
                        max_time.setMonth(max_time.getMonth() + 6);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                }
                if (max_time.getTime() > endtime.getTime()) max_time = new Date(endtime);
                graph_data = resortDangerData(graph_data, x_range);
                break;
        }
        var cur_time = new Date(min_time);
        cur_time.setHours(min_time.getHours());
        cur_time.setMinutes(0);
        cur_time.setSeconds(0);

        var date_labels = [];
        var totals_by_action = {};
        Object.keys(actions).map(id => {
            totals_by_action[id] = [];
        })
        var max_y = 0;
        while(cur_time.getTime() <= max_time.getTime()){
            date_labels.push(new Date(cur_time));
            if (graph_id == 'past_graph_danger'){
                if (x_range == 'day' || x_range == 'week' || x_range == 'month'){
                    var date_key = formatDateLine(cur_time);
                    if (x_range == 'week') date_key = formatYearWeekNum(cur_time);
                    if (x_range == 'month') date_key = formatYearMonth(cur_time);
                    if (graph_data[date_key] == undefined){
                        Object.keys(actions).map(id => {
                            totals_by_action[id].push(0);
                        })
                    } else {
                        Object.keys(actions).map(id => {
                            totals_by_action[id].push(graph_data[date_key][id]);
                            if (max_y < graph_data[date_key][id]) max_y = graph_data[date_key][id];
                        })
                    }
                } else {
                    var y_add_flag = false;
                    var y_value={};
                    Object.keys(actions).map(id => {
                        y_value[id] = 0;
                    })
                    Object.keys(graph_data).map((detect_time, index) => {
                        var detect_time_object = new Date(detect_time);
                        if (detect_time_object.getTime() >= cur_time.getTime() && detect_time_object.getTime() < cur_time.getTime() + grid_unit * 60 * 1000 && detect_time_object.getTime() <= max_time.getTime()){
                            // if (index == 0){
                            //     y_add_flag = true;
                            //     if  (detect_time_object.getTime() != cur_time.getTime()){
                            //         date_labels.push(detect_time_object);
                            //         Object.keys(actions).map(id => {
                            //             totals_by_action[id].push(0);
                            //         })
                            //     }
                            // } else {
                            //     date_labels.push(detect_time_object);
                            // }
                            Object.keys(actions).map(id => {
                                // if (graph_data[detect_time][id] != undefined){
                                //     totals_by_action[id].push(graph_data[detect_time][id]);
                                //     if (graph_data[detect_time][id] > max_y) max_y = graph_data[detect_time][id];
                                // } else {
                                //     totals_by_action[id].push(0);
                                // }
                                if (graph_data[detect_time][id] != undefined){
                                    y_value[id] += graph_data[detect_time][id];
                                }
                            })
                        }
                    })
                    Object.keys(actions).map(id => {
                        if (max_y < y_value[id]) max_y = y_value[id];
                        totals_by_action[id].push(y_value[id]);
                    })
                    // if (y_add_flag == false){
                    //     Object.keys(actions).map(id => {
                    //         totals_by_action[id].push(0);
                    //     })
                    // }
                }
            } else {
                var y_add_flag = false;
                var y_value={};
                Object.keys(actions).map(id => {
                    y_value[id] = 0;
                })
                Object.keys(graph_data).map((detect_time, index) => {
                    var detect_time_object = new Date(detect_time);
                    if (detect_time_object.getTime() >= cur_time.getTime() && detect_time_object.getTime() < cur_time.getTime() + grid_unit * 60 * 1000 && detect_time_object.getTime() <= max_time.getTime()){
                        // if (index == 0){
                        //     y_add_flag = true;
                        //     if  (detect_time_object.getTime() != cur_time.getTime()){
                        //         date_labels.push(detect_time_object);
                        //         Object.keys(actions).map(id => {
                        //             totals_by_action[id].push(0);
                        //         })
                        //     }
                        // } else {
                        //     date_labels.push(detect_time_object);
                        // }
                        Object.keys(actions).map(id => {
                            // if (graph_data[detect_time][id] != undefined){
                            //     totals_by_action[id].push(graph_data[detect_time][id]);
                            //     if (graph_data[detect_time][id] > max_y) max_y = graph_data[detect_time][id];
                            // } else {
                            //     totals_by_action[id].push(0);
                            // }
                            if (graph_data[detect_time][id] != undefined){
                                y_value[id] += graph_data[detect_time][id];
                            }
                        })
                    }
                })
                Object.keys(actions).map(id => {
                    if (max_y < y_value[id]) max_y = y_value[id];
                    totals_by_action[id].push(y_value[id]);
                })
                // if (y_add_flag == false){
                //     Object.keys(actions).map(id => {
                //         totals_by_action[id].push(0);
                //     })
                // }
            }
            switch(x_range){
                case 'time':
                    cur_time.setHours(cur_time.getHours() + 1);
                    break;
                case 'day':
                    cur_time.setDate(cur_time.getDate() + 1);
                    break;
                case 'week':
                    cur_time.setDate(cur_time.getDate() + 7);
                    break;
                case 'month':
                    cur_time.setMonth(cur_time.getMonth() + 1);
                    break;
                default:
                    cur_time.setMinutes(cur_time.getMinutes() + grid_unit);
                    break;
            }
        }
        var datasets = [];
        Object.keys(totals_by_action).map(action_id => {
            datasets.push({
                label:actions[action_id],
                data:totals_by_action[action_id],
                borderColor:color_set[action_id],
                backgroundColor:'white',
                lineTension:0,
            })
        });
        ctx.innerHTML = '';
        if (myLineChart[block_data.id] != undefined){
            myLineChart[block_data.id].destroy();
        }
        myLineChart[block_data.id] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: date_labels,
                    datasets,
                    mousemove: function(){
                        return;
                    },
                },
                options: {
                    legend: {
                        labels: {
                            fontSize: 12
                        }
                    },
                    responsive: true,
                    interaction: {
                        intersect: false,
                        axis: 'x'
                    },
                    title: {
                        display: false,
                        text: 'NGアクション毎の回数',
                        fontSize:12,
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                suggestedMax: max_y + 1,
                                suggestedMin: 0,
                                stepSize: parseInt((max_y + 2)/5) + 1,
                                fontSize: 12,
                                callback: function(value, index, values){
                                    return  value +  '回'
                                }
                            }
                        }],
                        xAxes:[{
                            type: 'time',
                            time: {
                                unit: x_unit,
                                displayFormats: x_display_format,
                                tooltipFormat:tooltipFormat,
                                distribution: 'series',
                                stepSize: grid_unit,
                                // format:'HH:mm'
                            },
                            ticks: {
                                fontSize: 12,
                                // max: max_time,
                                // min: min_time,
                            }
                        }]
                    },
                }
            });
    }

    function drawShelfThiefGraph(ctx, block_data, x_range, min_x_time = ''){
        var graph_id = $(ctx).attr('id');
        var graph_data = null;
        var grid_unit = 15;
        var x_unit = 'minute';
        var x_display_format = {'minute': 'H:mm'};
        var tooltipFormat = 'H:mm';
        var now = new Date();
        var min_time = new Date();
        var max_time = new Date();
        switch(graph_id){
            case 'live_graph_shelf':
            case 'live_graph_thief':
                graph_data = block_data.shelf_live_graph_data != undefined ? block_data.shelf_live_graph_data : {};
                if (graph_id == 'live_graph_thief'){
                    graph_data = block_data.thief_live_graph_data != undefined ? block_data.thief_live_graph_data : {};
                }
                max_time.setHours(now.getHours() + 1);
                max_time.setMinutes(0);
                max_time.setSeconds(0);

                x_range = parseInt(x_range);
                min_time.setHours((now.getHours() - (x_range -1 )) < 0 ? 0 : now.getHours() -(x_range -1 ));
                min_time.setMinutes(0);
                min_time.setSeconds(0);
                graph_data = resortGraphData(graph_data, x_range);
                if (x_range == 6) grid_unit = 30;
                if (x_range == 12 || x_range == 24) grid_unit = 60;
                break;
            case 'past_graph_shelf':
            case 'past_graph_thief':
                graph_data = block_data.shelf_past_graph_data != undefined ? block_data.shelf_past_graph_data : {};
                if (graph_id == 'past_graph_thief'){
                    graph_data = block_data.thief_past_graph_data != undefined ? block_data.thief_past_graph_data : {};
                }
                min_time = min_time != '' ? new Date(min_x_time): new Date();
                max_time = new Date(min_time);
                if (!isNaN(parseInt(x_range))) x_range = parseInt(x_range);
                var endtime = new Date(block_data.endtime);
                endtime.setHours(23);
                endtime.setMinutes(59);
                endtime.setSeconds(59);
                switch(x_range){
                    case 3:
                        grid_unit = 15;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 6:
                        grid_unit = 30;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 12:
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 24:
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'H:mm'};
                        tooltipFormat = "H:mm";
                        max_time.setHours(max_time.getHours() + parseInt(x_range));
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 'time':
                        grid_unit = 60;
                        x_unit = 'minute';
                        x_display_format = {'minute': 'DD日H時'};
                        tooltipFormat = "MM/DD H:mm";
                        max_time.setDate(max_time.getDate() + 1);
                        if (endtime.getMinutes() == 59){
                            endtime.setSeconds(endtime.getSeconds() + 1);
                        }
                        break;
                    case 'day':
                        grid_unit = 1;
                        x_unit = 'day';
                        x_display_format = {'day': 'M/DD'};
                        tooltipFormat = "YY/MM/DD";
                        max_time.setDate(max_time.getDate() + 7);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                    case 'week':
                        grid_unit = 1;
                        x_unit = 'week';
                        x_display_format = {'week': 'M/DD'};
                        tooltipFormat = "YY/MM/DD";
                        max_time.setDate(max_time.getDate() + 28);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                    case 'month':
                        grid_unit = 1;
                        x_unit = 'month';
                        x_display_format = {'month': 'YYYY/MM'};
                        tooltipFormat = "YY/MM";
                        max_time.setMonth(max_time.getMonth() + 6);
                        if (endtime.getMinutes() == 0){
                            endtime.setSeconds(endtime.getSeconds() - 1);
                        }
                        break;
                }
                if (max_time.getTime() > endtime.getTime()) max_time = new Date(endtime);
                graph_data = resortGraphData(graph_data, x_range);
                break;
        }
        var cur_time = new Date(min_time);
        cur_time.setHours(min_time.getHours());
        cur_time.setMinutes(0);
        cur_time.setSeconds(0);

        var time_labels = [];
        var y_data = [];
        var point_radius = [];

        while(cur_time.getTime() <= max_time.getTime()){
            time_labels.push(new Date(cur_time));
            point_radius.push(0);
            y_data.push(null);

            if (x_range == 'day' || x_range == 'week' || x_range == 'month'){
                var date_key = formatDateLine(cur_time);
                if (x_range == 'week') date_key = formatYearWeekNum(cur_time);
                if (x_range == 'month') date_key = formatYearMonth(cur_time);
                if (graph_data[date_key] != undefined){
                    y_data[y_data.length - 1] = graph_data[date_key];
                    point_radius[point_radius.length - 1] = 3;
                }
            } else {
                Object.keys(graph_data).map((time, index) => {
                    if (new Date(time).getTime() >= cur_time.getTime() && new Date(time).getTime() < cur_time.getTime() + grid_unit* 60 * 1000 && new Date(time).getTime() <= max_time.getTime()){
                        if (index == 0){
                            if (new Date(time).getTime() != cur_time.getTime()) {
                                time_labels.push(new Date(time));
                                point_radius.push(0);
                                y_data.push(null);
                            }
                        } else {
                            time_labels.push(new Date(time));
                            y_data.push(graph_data[time]);
                            point_radius.push(3);
                        }
                        point_radius[point_radius.length - 1] = 3;
                        y_data[y_data.length - 1] = graph_data[time];
                    }
                })
            }

            switch(x_range){
                case 'time':
                    cur_time.setHours(cur_time.getHours() + 1);
                    break;
                case 'day':
                    cur_time.setDate(cur_time.getDate() + 1);
                    break;
                case 'week':
                    cur_time.setDate(cur_time.getDate() + 7);
                    break;
                case 'month':
                    cur_time.setMonth(cur_time.getMonth() + 1);
                    break;
                default:
                    cur_time.setMinutes(cur_time.getMinutes() + grid_unit);
                    break;
            }
        }
        ctx.innerHTML = '';
        if (myLineChart[block_data.id] != undefined){
            myLineChart[block_data.id].destroy();
        }
        myLineChart[block_data.id] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: time_labels,
                    datasets: [{
                        label: '回',
                        data: y_data,
                        borderColor: "#42b688",
                        pointBackgroundColor:'red',
                        radius:point_radius,
                        lineTension:0,
                        fill:false,
                        spanGaps: true
                    }],
                    mousemove: function(){
                        return;
                    },
                },
                options: {
                    legend: {
                        labels: {
                            fontSize: 12
                        }
                    },
                    responsive: true,
                    interaction: {
                        intersect: false,
                        axis: 'x'
                    },
                    title: {
                        display: false,
                        text: 'NGアクション毎の回数',
                        fontSize:12,
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                suggestedMax: Math.max(...y_data) + 1,
                                suggestedMin: 0,
                                stepSize: parseInt((Math.max(...y_data) + 2)/5) + 1,
                                fontSize: 12,
                                callback: function(value, index, values){
                                    return  value +  '回'
                                }
                            }
                        }],
                        xAxes:[{
                            type: 'time',
                            time: {
                                unit: x_unit,
                                displayFormats: x_display_format,
                                tooltipFormat:tooltipFormat,
                                distribution: 'series',
                                stepSize: grid_unit,
                                // format:'HH:mm'
                            },
                            ticks: {
                                fontSize: 12,
                                // max: max_time,
                                // min: min_time,
                            }
                        }]
                    },
                }
            });
    }

    function refreshGraph(){
        //draw graph-----------------
        $('.graph-canvas').each(function(){
            var graph_id = $(this).attr('id');
            var block_data = JSON.parse($(this).next().val());
            var time_period = $(this).next().next().val();
            var min_x_time = $('.min-time', $(this).parent()).val();
            switch(graph_id){
                case 'live_graph_danger':
                case 'past_graph_danger':
                    drawDangerGraph($(this), block_data, time_period, formatDateTime(min_x_time));
                    break;
                case 'live_graph_pit':
                case 'past_graph_pit':
                    drawPitGraph($(this), block_data, time_period, formatDateTime(min_x_time));
                    break;
                case 'live_graph_shelf':
                case 'past_graph_shelf':
                case 'live_graph_thief':
                case 'past_graph_thief':
                    drawShelfThiefGraph($(this), block_data, time_period, formatDateTime(min_x_time));
                    break;
            }
        })
        //----------------------------------
    }
    function moveXRange(e, increament = 1){
        var min_time = $('.min-time', $(e).parent()).val();
        var time_period = $('.time_period', $(e).parent()).val();
        var search_period = $('.search_period', $(e).parent()).val();
        var block_data = JSON.parse($('.block-data', $(e).parent()).val());
        if (min_time != undefined && min_time != '' && time_period != undefined && time_period != ''){
            min_time = formatDateTime(min_time);
            var endtime = formatDateTime(block_data.endtime);
            endtime.setHours(24);
            endtime.setMinutes(0);
            endtime.setSeconds(0);
            var starttime = formatDateTime(block_data.starttime);
            if (!isNaN(parseInt(time_period))) time_period = parseInt(time_period);
            switch(time_period){
                case 3:
                if (increament == 1){
                    if (min_time.getHours() + 3 >= 24){
                        return;
                    }
                    min_time.setHours(min_time.getHours() + 3);
                } else {
                    if (min_time.getHours() - 3 < 0){
                        return;
                    }
                    min_time.setHours(min_time.getHours() - 3);
                }
                break;
            case 6:
                if (increament == 1){
                    if (min_time.getHours() + 6 >= 24){
                        return;
                    }
                    min_time.setHours(min_time.getHours() + 6);
                } else {
                    if (min_time.getHours() - 6 < 0){
                        return;
                    }
                    min_time.setHours(min_time.getHours() - 6);
                }
                break;
            case 12:
                if (increament == 1){
                    if (min_time.getHours() + 12 >= 24){
                        return;
                    }
                    min_time.setHours(min_time.getHours() + 12);
                } else {
                    if (min_time.getHours() - 12 < 0){
                        return;
                    }
                    min_time.setHours(min_time.getHours() - 12);
                }
                break;
            case 24:
                return;
            case 'time':
                if (increament == 1){
                    min_time.setDate(min_time.getDate() + 1);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time.setDate(min_time.getDate() - 1);
                        return;
                    }
                } else {
                    min_time.setDate(min_time.getDate() - 1);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time.setDate(min_time.getDate() + 1);
                        return;
                    }
                }
                break;
            case 'day':
                if (search_period < 7) return;
                if (increament == 1){
                    min_time.setDate(min_time.getDate() + 7);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time.setDate(min_time.getDate() - 7);
                        return;
                    }
                } else {
                    min_time.setDate(min_time.getDate() - 7);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time.setDate(min_time.getDate() + 7);
                        return;
                    }
                }
                break;
            case 'week':
                if (increament == 1){
                    min_time.setDate(min_time.getDate() + 28);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time.setDate(min_time.getDate() - 28);
                        return;
                    }
                } else {
                    min_time.setDate(min_time.getDate() - 28);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time.setDate(min_time.getDate() + 28);
                        return;
                    }
                }
                break;
            case 'month':
                if (search_period <= 180) return;
                if (increament == 1){
                    min_time.setMonth(min_time.getMonth() + 6);
                    if (min_time.getTime() >= endtime.getTime()) {
                        min_time.setMonth(min_time.getMonth() - 6);
                        return;
                    }
                } else {
                    min_time.setMonth(min_time.getMonth() - 6);
                    if (min_time.getTime() < starttime.getTime()) {
                        min_time.setMonth(min_time.getMonth() + 6);
                        return;
                    }
                }
                break;
            }
            $('.min-time', $(e).parent()).val(formatDateTimeStr(min_time));
        }
        refreshGraph();
    }

    function changeXlength(e, x_range){
        $('.period-button', $(e).parent()).each(function(){
            $(this).removeClass('selected');
        });
        $(e).addClass('selected');
        $('.time_period', $(e).parent().next()).val(x_range);
        $('.min-time', $(e).parent().next()).val($('.starttime', $(e).parent().prev()).val());
        refreshGraph();

        var block_data = JSON.parse($('.block-data', $(e).parent().next()).val());
        if (block_data.options != null){
            var options = JSON.parse(block_data.options);
            options.time_period = x_range;

            var changed_data = [];
            changed_data.push({
                id:block_data.id,
                options:JSON.stringify(options)
            });
            updateTopBlockData(changed_data);
        }
    }

    function changeSelectRule(){
        var changed_data = [];
        if ($('#selected_rule').val() > 0){
            changed_data.push({
                id:$('#selected_top_block').val(),
                selected_rule:$('#selected_rule').val(),
            })
        } else {
            var checked_rules = [];
            $('.rule_checkbox').each(function(){
                if ($(this).is(":checked")){
                    checked_rules.push($(this).val());
                }
            })
            changed_data.push({
                id:$('#selected_top_block').val(),
                selected_rules:checked_rules,
            })
        }

        updateTopBlockData(changed_data);
        setTimeout(() => {
            $('#top_form').submit();
        }, 500);
    }

    function changeSelectCamera(){
        var changed_data = [];
        changed_data.push({
            id:$('#selected_top_block').val(),
            selected_camera:$('#selected_camera').val(),
        })
        updateTopBlockData(changed_data);
        setTimeout(() => {
            $('#top_form').submit();
        }, 500);

    }

    function changePeriod(e, type='starttime', block_id){
        var changed_data = [];
        changed_data.push({
            id:block_id,
            [type]:e.value
        })
        updateTopBlockData(changed_data);
        setTimeout(() => {
            $('#top_form').submit();
        }, 500);
    }

    function drawFigure(figure_points, figure_color = null, ratio = 0.5, key){
        var figure_points = sortFigurePoints(figure_points);
        var drawing_point_data = [];
        figure_points.map(item => {
            drawing_point_data.push(item.x * ratio);
            drawing_point_data.push(item.y * ratio);
        });
        drawing_point_data.push(figure_points[0].x * ratio);
        drawing_point_data.push(figure_points[0].y * ratio);
        var figure_area = new Konva.Line({
            points: drawing_point_data,
            stroke: figure_color != null ? figure_color : 'black',
            strokeWidth: 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        layers[key].add(figure_area);
    }

    function drawRules(){
        //live video and draw stage on it-----------------
        $('.image-container').each(function(){
            var image_container_id = $(this).attr('id');
            if (image_container_id != ''){
                var streaming_video_id = 'streaming_video_' + image_container_id.replace('image_container_', '');
                var ratio = parseFloat($('#' + streaming_video_id).width()/1280).toFixed(4);
                stages[image_container_id] = new Konva.Stage({
                    container: $(this).attr('id'),
                    width: $('#' + streaming_video_id).width(),
                    height: $('#' + streaming_video_id).height(),
                });
                layers[image_container_id] = new Konva.Layer();
                stages[image_container_id].add(layers[image_container_id]);
                var block_data = JSON.parse($('.block-data', $(this).parent()).val());
                if (block_data.rules != undefined){
                    block_data.rules.map(rule_item => {
                        if (rule_item.points != undefined){
                            var points = JSON.parse(rule_item.points);
                            drawFigure(points, rule_item.color, ratio, image_container_id);
                        } else {
                            var blue_points = JSON.parse(rule_item.blue_points);
                            var red_points = JSON.parse(rule_item.red_points);
                            drawFigure(red_points, 'red', ratio, image_container_id);
                            drawFigure(blue_points, 'blue', ratio, image_container_id);
                        }
                    })
                }
            }
        })
        //-----------------------------------------------
    }
        $(document).ready(function() {
        refresshCameraImg();
        GridStack.init(options);
        setTimeout(() => {
            $('.video-open').click(function(){
                var video_path = $(this).parent().attr('video-path');
                videoPlay(this, video_path);
            })

            refreshGraph();
            $('#block-container').show();
            //scroll winodw-------------------
            var scroll_top = "<?php echo $scroll_top;?>";
            if (!isNaN(parseInt(scroll_top))){
                $(window).scrollTop(parseInt(scroll_top));
            }
            $(window).scroll(function(){
                $('#scroll_top').val($(this).scrollTop());
            })
            //-----------------------------
            drawRules();
            setTimeout(() => {
                $('.grid-stack').on('change', function(event) {
                    var changed_data = [];
                    $('.grid-stack-item', event.target).each(function(){
                        changed_data.push({
                            id:$(this).attr('data-id'),
                            gs_x:$(this).attr('gs-x'),
                            gs_y:$(this).attr('gs-y'),
                            gs_w:$(this).attr('gs-w'),
                            gs_h:$(this).attr('gs-h'),
                        });
                    });
                    var check_normal_flag = false;
                    changed_data.map(changed_item => {
                        if (changed_item.gs_w > 1) check_normal_flag = true
                    })
                    if (check_normal_flag == true){
                        updateTopBlockData(changed_data);
                    }
                    $('.image-container').each(function(){
                        var image_container_id = $(this).attr('id');
                        var streaming_video_id = 'streaming_video_' + image_container_id.replace('image_container_', '');
                        if (stages[image_container_id] != undefined){
                            stages[image_container_id].width($('#' + streaming_video_id).width());
                            stages[image_container_id].height($('#' + streaming_video_id).height());
                        }
                    })

                });
            }, 500);
        }, 500);
    })

</script>
@endsection
