@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.danger')}}">危険エリア侵入判定</a></li>
            <li>危険エリア検知リスト(アーカイブ)</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">危険エリア検知リスト(アーカイブ)</h2>
        </div>
        <div class='notice-area'>
            ※現在のプランでは検知してから検知リストに反映されるまで最低5分程度かかります。検知後表示までの時間を短くしたい場合はご相談ください。
        </div>
        <form action="{{route('admin.danger.list')}}" method="get" name="form1" id="form1">
        @csrf
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                        <ul class="date-list">
                            <li><h4>検出期間</h4></li>
                            <li>
                                <input type="date" id="starttime" name='starttime' value="{{ old('starttime', (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')))}}">
                            </li>
                            <li>～</li>
                            <li>
                                <input type="date" id="endtime" name='endtime' value="{{ old('endtime', (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'))}}">
                            </li>
                        </ul>
                        <ul class="date-list">
                            <li><h4>ルール</h4></li>
                            <li><a data-target="rule" class="modal-open setting">選択する</a></li>
                        </ul>
                        <input type= 'hidden' name='rule_ids' id = 'rule_id_input' value="{{ old('rule_ids', (isset($request) && $request->has('rule_ids'))?$request->rule_ids:'')}}"/>
                        <button type="submit" class="apply">絞り込む</button>
                    </div>
                </div>
            </div>
        </form>


        <button type="button" class="add-to-toppage <?php echo $from_top?'from_top':'' ?>" onclick="addDashboard({{config('const.top_block_type_codes')['detect_list_danger']}})">ダッシュボートへ追加</button>
        @if(!(count($danger_detections) > 0))
            <div class="no-data">検知データがありません。</div>
        @endif
        {{ $danger_detections->appends([
            'starttime'=> (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-d', strtotime('-1 week')),
            'endtime'=> (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-d'),
            'rule_ids' => isset($request) && $request->has('rule_ids')?$request->rule_ids:''
        ])->links('vendor.pagination.admin-pagination') }}
        <ul class="kenchi-list">
            @foreach ($danger_detections as $item)
            <?php
                $video_path = '';
                $video_path .= asset('storage/video/').'/';
                $video_path .= $item->video_file_path;
                if (isset($item->thumb_img_path) && $item->thumb_img_path != ''){
                    $thumb_path = asset('storage/thumb/').'/'.$item->thumb_img_path;
                } else {
                    $thumb_path = asset('assets/admin/img/samplepic.png');
                }
                $detection_name = isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action_statement')[$item->detection_action_id] : '';
            ?>
            <li>
                <div class="movie" video-path = '{{$video_path}}'>
                    <a data-target="movie0000" onclick="videoPlay('{{$video_path}}', '{{$item->points}}', '{{$item->color}}', '{{$detection_name}}')"
                        class="modal-open setting2 play">
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
                            <h2 style="cursor: pointer" class="icon-content" onclick="location.href='{{route('admin.danger.edit', ['danger' => $item->rule_id])}}'">検知内容</h2>
                            <p>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action_statement')[$item->detection_action_id] : ''}}</p>
                        </li>
                        {{-- <li>
                            <h2 class="icon-condition">検知条件</h2>
                            <dl>
                                <dt><p>{{isset($item->detection_action_id) && $item->detection_action_id > 0 ? config('const.action_cond_statement')[$item->detection_action_id] : ''}}</p></dt>
                                <dd>1人</dd>
                            </dl>
                        </li> --}}
                        {{-- <li>
                            <h2 class="icon-rule" onclick="location.href='{{route('admin.danger.edit', ['danger' => $item->rule_id])}}'">ルール</h2>
                            <dl>
                                <dd>{{isset($item->rule_name) ? $item->rule_name : ''}}</dd>
                            </dl>
                        </li> --}}
                    </ul>
                </div>
            </li>
            @endforeach
        </ul>
        {{ $danger_detections->appends([
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
                    <th>カメラNo</th>
                    <th>設置エリア</th>
                    <th>設置フロア</th>
                    <th>設置場所</th>
                    <th>アクション</th>
                    <th>カラー</th>
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
                        <td> {{$rule->camera_no}}</td>
                        <td>{{$rule->location_name}}</td>
                        <td>{{$rule->floor_number}}</td>
                        <td>{{$rule->installation_position}}</td>
                        <td>
                            @foreach (json_decode($rule->action_id) as $action_code)
                                <div>{{config('const.action')[$action_code]}}</div>
                            @endforeach
                        </td>
                        <td><input disabled type="color" value = "{{$rule->color}}"></td>
                        <td><img width="100px" src="{{$rule->img}}"/></td>
                    </tr>
                    @endforeach
                    @if(count($rules) == 0)
                    <tr>
                        <td colspan="8">登録されたルールがありません。ルールを設定してください</td>
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
        <video id = 'video-container' src = '' type= 'video/mp4' controls>
        </video>
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
    .textarea{
        max-width: 1200px;
        width:100%;
    }
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
    function selectRule(){
        var checked_rules = [];
        $('.rule_checkbox').each(function(){
            if ($(this).is(":checked")){
                checked_rules.push($(this).val());
            }
        })
        $('#rule_id_input').val(JSON.stringify(checked_rules));
    }

    function videoPlay(path, points, color, detection_name){
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

            var container = document.getElementById('image-container');
            var stage = new Konva.Stage({
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
    function addDashboard(block_type){
        var options = {
            starttime:formatDateLine($('#starttime').val()),
            endtime:formatDateLine($('#endtime').val())
        };
        addToToppage(block_type, options);
    }
    $(document).ready(function() {
        setInterval(() => {
            $.ajax({
                url : '/admin/CheckDetectData',
                method: 'post',
                data: {
                    type:'danger',
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
    })
</script>
@endsection
