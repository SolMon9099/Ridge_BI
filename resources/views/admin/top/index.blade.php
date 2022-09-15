@extends('admin.layouts.app')
@section('content')
<link href="{{ asset('assets/admin/css/gridstack.min.css') }}?{{ Carbon::now()->format('Ymdhis') }}" rel="stylesheet">
<div id="wrapper">
    <div id="r-content">
	    <div class="sp-ma">
            {{-- <h2 class="title">最近の検知</h2> --}}

            {{-- <div class="add-widget new-btn">
                <a onClick="addNewWidget()" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                        <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                        <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                    </svg>
                    項目追加
                </a>
            </div> --}}
            @include('admin.layouts.flash-message')
            @if (count($top_blocks) > 0)
                <div class="grid-stack">
                    @foreach ($top_blocks as $key => $item)
                    <div class="grid-stack-item" data-id = "{{$item->id}}" gs-x="{{$item->gs_x}}" gs-y="{{$item->gs_y}}" gs-w="{{$item->gs_w}}" gs-h="{{$item->gs_h}}">
                        <div class="grid-stack-item-content">
                            <div class="gear-block">
                                <button type="" class="" onclick="toggleMenu(this, {{$item->id}})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="fill: rgba(100, 100, 100, 1);transform: ;msFilter:;">
                                        <path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"></path>
                                        <path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"></path>
                                    </svg>
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
                            @if($item->block_type == config('const.top_block_type_codes')['live_video_danger'] || $item->block_type == config('const.top_block_type_codes')['live_video_pit'])
                                @if(isset($item->cameras) && count($item->cameras) > 0)
                                    <div class="camera-id">カメラID： {{$item->selected_camera->camera_id}}</div>
                                    <div class="streaming-video">
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
                                        <a data-target="movie0000" onclick="videoPlay('{{$video_path}}')" class="modal-open setting2 play">
                                            <img src="{{$thumb_path}}"/>
                                        </a>
                                    </div>
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
                                    <table class="danger-detect-list">
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
                                <div class="no-data">検知データがありません。</div>
                            @elseif($item->block_type == config('const.top_block_type_codes')['past_graph_danger'])
                                <div class="no-data">検知データがありません。</div>
                            @else
                                <div class="no-data">検知データがありません。</div>
                            @endif
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
                            <th>カメラ画像確認</th>
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
<div id="dialog-confirm" title="test" style="display:none">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
    <span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>

<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .sp-ma{
        padding-top: 50px;
    }
    .add-widget{
        text-align: right;
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
    .live-stream-title{
        font-size: 18px;
    }
    .camera-id{
        font-size:14px;
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
</style>
<script src="{{ asset('assets/admin/js/gridstack-all.js?2') }}"></script>
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/helper.js?2') }}"></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>
<script>
    function videoPlay(path){
        var video = document.getElementById('video-container');
        video.pause();
        $('#video-container').attr('src', path);
        video.play();
    }

    function gearClick(block_item, action){
        $('[data-menu-id="'+ block_item.id + '"]').hide();
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
                    // オーバーレイ用の要素を追加
                    $('body').append('<div class="modal-overlay"></div>');
                    // オーバーレイをフェードイン
                    $('.modal-overlay').fadeIn('fast');
                    // モーダルコンテンツを囲む要素を追加
                    $('#camera').wrap("<div class='modal-wrap'></div>");
                    // モーダルコンテンツを囲む要素を表示
                    $('.modal-wrap').fadeIn();
                    $('#camera').show();

                    $('#camera').fadeIn('fast');
                    // モーダルコンテンツをクリックした時はフェードアウトしない
                    $('.textarea').click(function (e) {
                        e.stopPropagation();
                    });
                    // 「.modal-overlay」あるいは「.modal-close」をクリック
                    $('.modal-wrap, .modal-close, .ok').off().click(function () {
                        // モーダルコンテンツとオーバーレイをフェードアウト
                        $('#camera').fadeOut('fast');
                        $('.modal-overlay').fadeOut('fast', function () {
                            // html、bodyの固定解除
                            $('html, body').removeClass('lock');
                            // オーバーレイを削除
                            $('.modal-overlay').remove();
                            // モーダルコンテンツを囲む要素を削除
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
                        tr_record += '<div class="checkbtn-wrap">';
                        tr_record += '<input class="selected_camera" name="selected_camera" value = "' + camera.id + '" type="radio" id="camera' + camera.id + '"' + checked + '/>';
                        tr_record += '<label for="camera' + camera.id + '"></label>';
                        tr_record += '</div>';
                        tr_record += '</td>';

                        tr_record += '<td>' + camera.camera_id + '</td>';
                        tr_record += '<td>' + camera.location_name + '</td>';
                        tr_record += '<td>' + camera.installation_position + '</td>';
                        tr_record += '<td><img width="100px" src="' + camera.img + '"/></td>';
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

    function toggleMenu(e, id){
        // $(e).toggleClass("on");
        $('[data-menu-id="'+ id + '"]').fadeToggle(5);
    }

    var options = { // put in gridstack options here
        // disableOneColumnMode: true, // for jfiddle small window size
        float: false
    };
    var grid = GridStack.init(options);
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
            }});
    });


</script>
@endsection
