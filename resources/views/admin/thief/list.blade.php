@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.thief')}}">大量盗難検知</a></li>
            <li>検知リスト</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">検知リスト</h2>
        </div>
        <div class='notice-area'>
            ※現在のプランでは検知してから検知リストに反映されるまで最低5分程度かかります。検知後表示までの時間を短くしたい場合はご相談ください。
        </div>
        <form action="{{route('admin.thief.list')}}" method="get" name="form1" id="form1">
        @csrf
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                        <ul class="date-list">
                            <li><h4>検出期間</h4></li>
                            <?php
                                $starttime = (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week'));
                                $endtime = (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d');
                            ?>
                            <li style="width:113px;">
                                <input type="date" id="starttime" name='starttime' onchange="$('#form1').submit()"
                                    max="{{strtotime(old('endtime', $endtime)) > strtotime(date('Y-m-d')) ? date('Y-m-d') : old('endtime', $endtime)}}"
                                    value="{{ old('starttime', $starttime)}}">
                            </li>
                            <li>～</li>
                            <li>
                                <input type="date" id="endtime" name='endtime' onchange="$('#form1').submit()" max="{{date('Y-m-d')}}" min="{{ old('starttime', $starttime)}}"
                                    value="{{ old('endtime', $endtime)}}">
                            </li>
                        </ul>
                        <ul class="date-list">
                            <li><h4>ルール</h4></li>
                            <li><a data-target="rule" class="modal-open setting">選択する</a></li>
                        </ul>
                        <input type= 'hidden' name='rule_ids' id = 'rule_id_input' value="{{ old('rule_ids', (isset($request) && $request->has('rule_ids'))?$request->rule_ids:'')}}"/>
                        {{-- <button type="submit" class="apply">絞り込む</button> --}}
                    </div>
                </div>
            </div>
        </form>
        @if(count($thief_detections) > 0)
            <button type="button" class="add-to-toppage <?php echo isset($from_top) && $from_top == true ?'from_top':'' ?>" onclick="addDashboard({{config('const.top_block_type_codes')['detect_list_thief']}})">ダッシュボートへ追加</button>
        @endif
        @if(!(count($thief_detections) > 0))
            <div class="no-data">検知データがありません。</div>
        @endif
        @include('admin.layouts.flash-message')
        {{ $thief_detections->appends([
            'starttime'=> (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')),
            'endtime'=> (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'),
            'rule_ids' => isset($request) && $request->has('rule_ids')?$request->rule_ids:''
        ])->links('vendor.pagination.admin-pagination') }}
        <ul class="kenchi-list">
            @foreach ($thief_detections as $item)
            <?php
                $video_path = '';
                $video_path .= asset('storage/video/').'/';
                $video_path .= $item->video_file_path;

                if (isset($item->thumb_img_path) && $item->thumb_img_path != ''){
                    $thumb_path = asset('storage/thumb/').'/'.$item->thumb_img_path;
                } else {
                    $thumb_path = asset('assets/admin/img/samplepic.png');
                }
            ?>
            <li>
                <div class="movie" video-path = '{{$video_path}}'>
                    <a data-target="movie0000" onclick="videoPlay('{{$video_path}}', '{{$item->points}}', '{{$item->color}}')"
                    class="modal-open setting2 play">
                        <img src="{{$thumb_path}}"/>
                    </a>
                    <div class="cap">
                        <time>{{date('Y/m/d H:i', strtotime($item->starttime))}}</time>
                    </div>
                </div>
                <div class="text">
                    <p class="camera-id">カメラID:{{$item->serial_no}}</p>
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
                            <h2 style="cursor: pointer" class="icon-content" onclick="location.href='{{route('admin.thief.edit', ['thief' => $item->rule_id])}}'">ルール</h2>
                            <dl>
                                <dd style="padding-top: 3px;">大量盗難を検知</dd>
                                <dd><input type="color" readonly value="{{$item->color}}"/></dd>
                            </dl>
                        </li>
                        <li>
                            <h2 style="cursor: pointer" class="icon-rule" onclick="location.href='{{route('admin.thief.edit', ['thief' => $item->rule_id])}}'">ルール名</h2>
                            <dl>
                                <dd>{{isset($item->rule_name) ? $item->rule_name : ''}}</dd>
                            </dl>
                        </li>
                    </ul>
                </div>
            </li>
            @endforeach
        </ul>
        {{ $thief_detections->appends([
            'starttime'=> (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')),
            'endtime'=> (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'),
            'rule_ids' => isset($request) && $request->has('rule_ids')?$request->rule_ids:''
        ])->links('vendor.pagination.admin-pagination') }}
    </div>
</div>
<!--MODAL -->
<div id="rule" class="modal-content">
    <div class="textarea">
        <div class="listing">
            <h3>検索対象となる、ルールを選択してください</h3>
            <div class="scroll active sp-pl0">
            <table class="table2 text-centre">
                <thead>
                <tr>
                    <th class="w10"></th>
                    <th>ルール名</th>
                    <th>カメラNo</th>
                    <th>設置エリア</th>
                    <th>設置フロア</th>
                    <th>設置場所</th>
                    <th>ハンガー</th>
                    <th>カラー</th>
                    <th>ルールの設定期間</th>
                    <th>カメラ画像確認</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        $selected_rule_ids = old('rule_ids', (isset($request) && $request->has('rule_ids'))?$request->rule_ids:'');
                        if ($selected_rule_ids != ''){
                            $selected_rule_ids = json_decode($selected_rule_ids);
                        } else {
                            $selected_rule_ids = [];
                        }
                    ?>
                    @foreach ($rules as $rule)
                    <tr>
                        <td class="stick-t">
                            <div class="checkbtn-wrap">
                                @if (in_array($rule->id, $selected_rule_ids))
                                    <input value = '{{$rule->id}}' class='rule_checkbox' type="checkbox" id="{{'rule-'.$rule->id}}" checked>
                                @else
                                    <input value = '{{$rule->id}}' class='rule_checkbox' type="checkbox" id="{{'rule-'.$rule->id}}">
                                @endif
                                <label for="{{'rule-'.$rule->id}}" class="custom-style"></label>
                            </div>
                        </td>
                        <td>{{$rule->name}}</td>
                        <td>{{$rule->serial_no}}</td>
                        <td>{{$rule->location_name}}</td>
                        <td>{{$rule->floor_number}}</td>
                        <td>{{$rule->installation_position}}</td>
                        <td><input disabled type="color" value = "{{$rule->hanger}}"></td>
                        <td><input disabled type="color" value = "{{$rule->color}}"></td>
                        <td>{{date('Y-m-d', strtotime($rule->created_at)).'～'.($rule->deleted_at != null ? date('Y-m-d', strtotime($rule->deleted_at)) : '')}}</td>
                        <td>
                            @if(Storage::disk('recent_camera_image')->exists($rule->device_id.'.jpeg'))
                                <img width="100px" src="{{asset('storage/recent_camera_image/').'/'.$rule->device_id.'.jpeg'}}"/>
                            @else
                                カメラ停止中
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(count($rules) == 0)
                    <tr>
                        <td colspan="6">登録された大量盗難検知のルールがありません。ルールを設定してください</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="modal-set">
                @if(count($rules) > 0)
                <button onclick="selectRule()" class="modal-close">設 定</button>
                @endif
            </div>
            </div>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<!--MODAL -->
<div id="movie0000" class="modal-content">
<div class="textarea">
    <div class="v">
        <div id="image-container"></div>
        <video id = 'video-container' src = '' type= 'video/mp4' controls></video>
        <p class="video-notice detect-content"></p>
        <p class="video-notice">動画の30秒あたりが検知のタイミングになります。</p>
    </div>
</div>
<p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<div id="alert-modal" title="test" style="display:none">
    <p><span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .v{
        position: relative;
    }
    #image-container{
        position: absolute;
        z-index: 0;
        cursor: pointer;
    }
</style>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script>
    var stage = null;
    var initial_width = null;
    function selectRule(){
        var checked_rules = [];
        $('.rule_checkbox').each(function(){
            if ($(this).is(":checked")){
                checked_rules.push($(this).val());
            }
        })
        $('#rule_id_input').val(JSON.stringify(checked_rules));
        $('#form1').submit();
    }

    function videoPlay(path, points, color, detection_name = ''){
        var video = document.getElementById('video-container');
        video.pause();
        $('#video-container').attr('src', path);
        video.play();
        points = JSON.parse(points);
        setTimeout(() => {
            var width = $('#video-container').width();
            var height = $('#video-container').height();
            $('#image-container').width(width);
            $('#image-container').height(height);
            initial_width = width;

            var container = document.getElementById('image-container');
            stage = new Konva.Stage({
                container: 'image-container',
                width: container.clientWidth,
                height: container.clientHeight,
            });
            layer = new Konva.Layer();
            stage.add(layer);
            var ratio = parseFloat(width/1280).toFixed(4);

            function drawFigure(figure_points, figure_color = null, ratio = 0.5){
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
                layer.add(figure_area);
            }

            drawFigure(points, color, ratio);
            $('.detect-content').html(detection_name);
        }, 1000);
    }

    $(document).ready(function() {
        setInterval(() => {
            $.ajax({
                url : '/admin/CheckDetectData',
                method: 'post',
                data: {
                    type:'thief',
                    endtime:formatDateLine(new Date($('#endtime').val())),
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    last_record_id : "<?php echo $last_number;?>"
                },
                error : function(){
                    console.log('failed');
                },
                success: function(result){
                    console.log('success', result);
                    if (result == 1){
                        $('#form1').submit();
                    }
                }
            })
        }, 60000);
        window.addEventListener('resize', function(){
            var width = $('#video-container').width();
            var height = $('#video-container').height();
            if (width > 0 && height > 0 && initial_width > 0){
                $('#image-container').width(width);
                $('#image-container').height(height);
                stage.width(width);
                stage.height(height);
                var scale = width/initial_width;
                stage.scale({x:scale, y:scale});
            }
        });
    })
</script>
@endsection
