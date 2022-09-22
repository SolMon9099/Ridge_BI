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
    .menu-li{
        padding-left:10px;
    }
    .grid-stack{
        width:100%;
        margin-top:15px;
        background: #DDEBF7;
    }
    .grid-stack-item-content {
        background-color: white;
        padding: 12px;
    }
    .grid-stack-item-content::-webkit-scrollbar {
        /* background: lightgray; */
    }
    .grid-contents{
        height: calc(100% - 25px);
        position: relative;
    }
    .ui-resizable-handle{
        right:25px!important;
        transform: rotate(45deg)!important;
        background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDUxMi4wNCA1MTIuMDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMi4wNCA1MTIuMDQ7ZmlsbDogcmdiYSgwLCA5OCwgMjIyLCAxKTsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik01MDguOTMzLDI0OC4zNTNMNDAyLjI2NywxNDEuNjg3Yy00LjI2Ny00LjA1My0xMC45ODctMy45NDctMTUuMDQsMC4yMTNjLTMuOTQ3LDQuMTYtMy45NDcsMTAuNjY3LDAsMTQuODI3DQoJCQlsODguNDI3LDg4LjQyN0gzNi40bDg4LjQyNy04OC40MjdjNC4wNTMtNC4yNjcsMy45NDctMTAuOTg3LTAuMjEzLTE1LjA0Yy00LjE2LTMuOTQ3LTEwLjY2Ny0zLjk0Ny0xNC44MjcsMEwzLjEyLDI0OC4zNTMNCgkJCWMtNC4xNiw0LjE2LTQuMTYsMTAuODgsMCwxNS4wNEwxMDkuNzg3LDM3MC4wNmM0LjI2Nyw0LjA1MywxMC45ODcsMy45NDcsMTUuMDQtMC4yMTNjMy45NDctNC4xNiwzLjk0Ny0xMC42NjcsMC0xNC44MjcNCgkJCUwzNi40LDI2Ni41OTNoNDM5LjE0N0wzODcuMTIsMzU1LjAyYy00LjI2Nyw0LjA1My00LjM3MywxMC44OC0wLjIxMywxNS4wNGM0LjA1Myw0LjI2NywxMC44OCw0LjM3MywxNS4wNCwwLjIxMw0KCQkJYzAuMTA3LTAuMTA3LDAuMjEzLTAuMjEzLDAuMjEzLTAuMjEzbDEwNi42NjctMTA2LjY2N0M1MTMuMDkzLDI1OS4zNCw1MTMuMDkzLDI1Mi41MTMsNTA4LjkzMywyNDguMzUzeiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K')!important;
    }
    .video-play{
        display: none;
        position: absolute;
        top: 25px;
        width: 80%;
        left: 20px;
    }
    .video-open{
        display: inline-block;
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
        top:5px;
        cursor: pointer;
    }
    .gear-block{
        position: absolute;
        right: 5px;
        top: 5px;
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
    .no-data{
        font-size: 16px;
        text-align: center;
        border:1px solid lightgray;
        margin-top:40px;
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
        margin: 0 0 10px 0;
        cursor: pointer;
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
        width:37%;
    }
    .area{
        width:21%;
    }
    .location{
        width:21%;
    }
    .action{
        width:21%;
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
                    <a class="total-menu-letter" onClick="showEditMenu(this)" href="#">編集</a>
                </div>
                <ul class="user-menu">
                    <h2>表示項目を追加</h2>
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
                                    @endif
                                @else
                                    @if (!$general_user_flag || in_array($url, $manager_allowed_pages))
                                        <li class="menu-li"><a href="{{route($url).'?from_top=true'}}">{{$item['name']}}</a></li>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="fill: rgba(0, 98, 222, 1);transform: ;msFilter:;">
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
                            <form id="frm_delete_{{ $item->id }}" action="{{ route('admin.top.delete', ['top'=> $item->id]) }}" method="POST" style="display: none;">
                                @csrf
                                @method('delete')
                            </form>
                            <div class="live-stream-title">{{config('const.top_block_titles')[$item->block_type]}}</div>
                            <div class='grid-contents'>
                                @if($item->block_type == config('const.top_block_type_codes')['live_video_danger'] || $item->block_type == config('const.top_block_type_codes')['live_video_pit'])
                                    @if(isset($item->cameras) && count($item->cameras) > 0 && isset($item->selected_camera))
                                        <div class="camera-id">カメラID： {{$item->selected_camera->camera_id}}</div>
                                        <div id={{"image_container_".$item->id}} class="image-container"></div>
                                        <div class="streaming-video" id = {{'streaming_video_'.$item->id}}>
                                            <?php
                                                foreach($item->cameras as $camera_item){
                                                    if ($item->selected_camera->camera_id == $camera_item->camera_id){
                                                        $item->selected_camera->access_token = $camera_item->access_token;
                                                    }
                                                }
                                            ?>
                                            <safie-streaming-player data-camera-id='{{$item->selected_camera->camera_id}}' data-token='{{$item->selected_camera->access_token}}'>
                                            </safie-streaming-player>
                                        </div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['recent_detect_danger'])
                                    @if(isset($item->danger_detection))
                                        <?php
                                            $video_path = '';
                                            $video_path .= asset('storage/video/').'/';
                                            $video_path .= $item->danger_detection->video_file_path;

                                            $thumb_path = asset('storage/thumb/').'/'.$item->danger_detection->thumb_img_path;
                                        ?>
                                        <div class="camera-id">カメラID：{{$item->danger_detection->camera_no}}</div>
                                        <div class="movie" video-path = "{{$video_path}}">
                                            <a data-target="movie0000"
                                                {{-- onclick="videoPlay(this, '{{$video_path}}')"  --}}
                                                class="video-open setting2 play">
                                                <img src="{{$thumb_path}}"/>
                                            </a>
                                        </div>
                                        <video style="" class = 'video-play' src = '{{$video_path}}' type= 'video/mp4' controls></video>
                                        <div class="cap">検知時間：<time>{{date('Y/m/d H:i', strtotime($item->danger_detection->starttime))}}</time></div>
                                        <div class="cap">検知条件：
                                            <time>
                                                {{isset($item->danger_detection->detection_action_id) && $item->danger_detection->detection_action_id > 0 ? config('const.action_cond_statement')[$item->danger_detection->detection_action_id] : ''}}
                                            </time>
                                        </div>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['detect_list_danger'])
                                    @if (isset($item->danger_detections) && count($item->danger_detections) > 0)
                                        <table class="danger-detect-list list-table">
                                            <thead>
                                                <tr>
                                                    <th class="time">時間</th>
                                                    <th class="area">設置エリア</th>
                                                    <th class="location">設置場所</th>
                                                    <th class="action">アクション</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($item->danger_detections as $detection_item)
                                                <tr>
                                                    <td>{{date('Y/m/d H:i', strtotime($detection_item->starttime))}}</td>
                                                    <td>{{$detection_item->location_name}}</td>
                                                    <td>{{$detection_item->installation_position}}</td>
                                                    <td>{{$detection_item->detection_action_id > 0 ? config('const.action')[$detection_item->detection_action_id] : ''}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @elseif($item->block_type == config('const.top_block_type_codes')['live_graph_danger'])
                                    <?php $danger_live_graph_data = $item->danger_live_graph_data;?>
                                    <div class="graph-area">
                                        <canvas id="live_graph_danger" class="graph-canvas"></canvas>
                                    </div>
                                    {{-- <div class="no-data">検知データがありません。</div> --}}
                                @elseif($item->block_type == config('const.top_block_type_codes')['past_graph_danger'])
                                    <?php $danger_past_graph_data = $item->danger_past_graph_data;?>
                                    <div class="graph-area">
                                        <canvas id="past_graph_danger" class="graph-canvas"></canvas>
                                    </div>
                                    {{-- <div class="no-data">検知データがありません。</div> --}}
                                @elseif($item->block_type == config('const.top_block_type_codes')['detect_list_pit'])
                                    @if (isset($item->pit_detections) && count($item->pit_detections) > 0)
                                        <table class="pit-detect-list list-table">
                                            <thead>
                                                <tr>
                                                    <th class="time">時間</th>
                                                    <th class="area">設置エリア</th>
                                                    <th class="location">設置場所</th>
                                                    <th class="action">検知内容</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($item->pit_detections as $detection_item)
                                                <tr>
                                                    <td>{{date('Y/m/d H:i', strtotime($detection_item->starttime))}}</td>
                                                    <td>{{$detection_item->location_name}}</td>
                                                    <td>{{$detection_item->installation_position}}</td>
                                                    <td>
                                                        {{$detection_item->nb_entry > $detection_item->nb_exit ? '入場 '.($detection_item->nb_entry - $detection_item->nb_exit) :  '退場 '.($detection_item->nb_exit - $detection_item->nb_entry)}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="no-data">検知データがありません。</div>
                                    @endif
                                @else
                                    <div class="no-data">検知データがありません。</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                {{-- <div class="no-data">現在設定されていません</div> --}}
            @endif
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

<!--MODAL -->
<div id="camera" class="modal-content">
    <div class="textarea">
        <div class="listing">
            <form action="{{route('admin.top.update')}}" method="post" name="form" id="camera_form">
            @csrf
                <input name="selected_top_block" id = 'selected_top_block' type="hidden"/>
                <input name="selected_camera_data" id = 'selected_camera' type="hidden"/>
                <div class="scroll active sp-pl0">
                    <table class="table2 text-centre">
                        <thead>
                        <tr>
                            <th class="w10"></th>
                            <th>カメラNo</th>
                            <th>設置エリア</th>
                            <th>設置場所</th>
                            {{-- <th>カメラ画像確認</th> --}}
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="modal-set">
                        <button type="submit" class="modal-close">設 定</button>
                    </div>
                </div>
            </form>
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
<div id="dialog-confirm" title="test" style="display:none">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
    <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<script src="{{ asset('assets/admin/js/gridstack-all.js?2') }}"></script>
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}" defer></script>
<script src="{{ asset('assets/admin/js/helper.js?2') }}" defer></script>
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
    var actions = <?php echo json_encode(config('const.action'));?>;
    GridStack.init(options);
    var stages = {};

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
            $('.total-menu-letter').html('編集');
        }
    }

    function gearClick(block_item, action){
        $('[data-menu-id="'+ block_item.id + '"]').hide();
        $('.close-gear-icon').hide();
        $('svg', $('.gear-block')).show();
        switch(action){
            case 'delete':
                delete_id = block_item.id;
                helper_confirm("dialog-confirm", "削除", "削除します。<br />よろしいですか？", 300, "確認", "閉じる", function(){
                    var frm_id = "#frm_delete_" + delete_id;
                    $(frm_id).submit();
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
                        if (camera.id == block_item.selected_camera.id) checked = 'checked';
                        var tr_record = '<tr>';
                        tr_record += '<td class="stick-t">';
                        tr_record += '<div class="checkbtn-wrap radio-wrap-div">';
                        tr_record += '<input class="selected_camera" name="selected_camera" value = "' + camera.id + '" type="radio" id="camera' + camera.id + '"' + checked + '/>';
                        tr_record += '<label for="camera' + camera.id + '"></label>';
                        tr_record += '</div>';
                        tr_record += '</td>';

                        tr_record += '<td>' + camera.camera_id + '</td>';
                        tr_record += '<td>' + camera.location_name + '</td>';
                        tr_record += '<td>' + camera.installation_position + '</td>';
                        // tr_record += '<td><img width="100px" src="' + camera.img + '"/></td>';
                        tr_record += '</tr>';
                        $('tbody', $('#camera')).append(tr_record);
                        $('.selected_camera').click(function(){
                            var selected_camera_id = $(this).attr('id');
                            selected_camera_id = selected_camera_id.replace('camera', '');
                            var selected_camera = block_item.cameras.find(x => x.id == selected_camera_id);
                            delete selected_camera.img;
                            $('#selected_camera').val(JSON.stringify(selected_camera));
                        })
                    })
                }
                break;
            case 'change_period':
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
            }
        });

        var conent_node = $('.grid-contents', $(e).parent().parent());
        $('.extentsion-content', $('#extension-modal')).append('<div class="modal-title">' + title + '</div>')
        $('.extentsion-content', $('#extension-modal')).append(conent_node.clone());
        $('.graph-canvas', conent_node).attr('id', '');
        refreshGraph();
        load();
        $('.video-open', $('#extension-modal')).unbind();
        $('.video-open', $('#extension-modal')).click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            if($(this).hasClass('play')){
                allVideoStop();
                $('.video-play', $('#extension-modal')).show();
                $('.video-play', $('#extension-modal'))[0].play();
            }
        })
        //-------------------------------

        $('.modal-wrap,.modal-close, .ok').off().click(function () {
            $('#extension-modal').fadeOut('fast');
            $('.modal-overlay').fadeOut('fast', function () {
                $('html, body').removeClass('lock');
                $('.modal-overlay').remove();
                $('#extension-modal').unwrap("<div class='modal-wrap'></div>");
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
                load();
                refreshGraph();
            });
        });
    }

    function toggleMenu(e, id){
        $(e).toggleClass("on");
        $('[data-menu-id="'+ id + '"]').fadeToggle(5);
        if ($(e).hasClass('on')){
            $('svg', $(e)).hide();
            $('.close-gear-icon', $(e)).show();
        } else {
            $('svg', $(e)).show();
            $('.close-gear-icon', $(e)).hide();
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
                    defaultAutoPlay:true
                };
            }
        })

    }
    function resortData(data, time_period){
        var temp = {};
        switch(time_period){
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

    function drawDangerGraph(ctx, graph_data){
        var graph_id = $(ctx).attr('id');
        var time_period = 6;
        var grid_unit = 30;
        var x_unit = 'minute';
        var x_display_format = {'minute': 'H:mm'};
        var tooltipFormat = 'H:mm';
        var now = new Date();
        var min_time = new Date();
        var max_time = new Date();
        switch(graph_id){
            case 'live_graph_danger':
                max_time.setHours(now.getHours() + 1);
                max_time.setMinutes(0);
                max_time.setSeconds(0);

                min_time.setHours((now.getHours() - (time_period -1 )) < 0 ? 0 : now.getHours() -(time_period -1 ));
                min_time.setMinutes(0);
                min_time.setSeconds(0);
                graph_data = resortData(graph_data, 'time');
                break;
            case 'past_graph_danger':
                x_unit = 'day';
                x_display_format = {'day': 'M/DD'};
                tooltipFormat = "YY/MM/DD";
                min_time.setDate(min_time.getDate() - 7);
                min_time.setHours(0);
                min_time.setMinutes(0);
                min_time.setSeconds(0);
                max_time.setHours(0);
                max_time.setMinutes(0);
                max_time.setSeconds(0);
                grid_unit = 1;
                graph_data = resortData(graph_data, 'day');
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
        var props = 1;
        if (graph_id == 'past_graph_danger') props = 1440;
        while(cur_time.getTime() <= max_time.getTime()){
            date_labels.push(new Date(cur_time));
            if (graph_id == 'past_graph_danger'){
                var date_key = formatDateLine(cur_time);
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
                Object.keys(graph_data).map((detect_time, index) => {
                    var detect_time_object = new Date(detect_time);
                    if (detect_time_object.getTime() >= cur_time.getTime() && detect_time_object.getTime() < cur_time.getTime() + grid_unit * props * 60 * 1000){
                        if (index == 0){
                            y_add_flag = true;
                            if  (detect_time_object.getTime() != cur_time.getTime()){
                                date_labels.push(detect_time_object);
                                Object.keys(actions).map(id => {
                                    totals_by_action[id].push(0);
                                })
                            }
                        } else {
                            date_labels.push(detect_time_object);
                        }
                        Object.keys(actions).map(id => {
                            if (graph_data[detect_time][id] != undefined){
                                totals_by_action[id].push(graph_data[detect_time][id]);
                                if (graph_data[detect_time][id] > max_y) max_y = graph_data[detect_time][id];
                            } else {
                                totals_by_action[id].push(0);
                            }
                        })
                    }
                })
                if (y_add_flag == false){
                    Object.keys(actions).map(id => {
                        totals_by_action[id].push(0);
                    })
                }
            }
            cur_time.setMinutes(cur_time.getMinutes() + grid_unit * props);

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

        var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: date_labels,
                    datasets
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
                        display: true,
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
        })
        jQuery.ajax({
            url : '/admin/AjaxUpdate',
            method: 'post',
            data: {
                changed_data,
                _token:$('meta[name="csrf-token"]').attr('content'),
            },

            error : function(){
                console.log('failed');
            },
            success: function(result){
                console.log(result);
            }
        });
        $('.image-container').each(function(){
            var image_container_id = $(this).attr('id');
            var streaming_video_id = 'streaming_video_' + image_container_id.replace('image_container_', '');
            if (stages[image_container_id] != undefined){
                stages[image_container_id].width($('#' + streaming_video_id).width());
                stages[image_container_id].height($('#' + streaming_video_id).height());
            }
        })

    });

    function refreshGraph(){
        //draw graph-----------------
        $('.graph-canvas').each(function(){
            var graph_id = $(this).attr('id');
            switch(graph_id){
                case 'live_graph_danger':
                    graph_data = <?php echo isset($danger_live_graph_data) ? json_encode($danger_live_graph_data) : json_encode([]);?>;
                    drawDangerGraph($(this), graph_data);
                    break;
                case 'past_graph_danger':
                    graph_data = <?php echo isset($danger_past_graph_data) ? json_encode($danger_past_graph_data) : json_encode([]);?>;
                    drawDangerGraph($(this), graph_data);
                    break;
            }
        })
        //----------------------------------
    }

    $(document).ready(function() {
        //live video and draw stage on it-----------------
        $('.image-container').each(function(){
            var image_container_id = $(this).attr('id');
            var streaming_video_id = 'streaming_video_' + image_container_id.replace('image_container_', '');
            stages[image_container_id] = new Konva.Stage({
                container: $(this).attr('id'),
                width: $('#' + streaming_video_id).width(),
                height: $('#' + streaming_video_id).height(),
            });
            var layer = new Konva.Layer();
            stages[image_container_id].add(layer);
        })
        //-----------------------------------------------
        $('.video-open').click(function(){
            var video_path = $(this).parent().attr('video-path');
            videoPlay(this, video_path);
        })

        refreshGraph();

    })

</script>
@endsection
