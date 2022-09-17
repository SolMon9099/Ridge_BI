<?php
    $selected_rule = null;
    foreach ($rules as $rule) {
        if ($rule->points != null && $rule->points != '') $rule->points = json_decode($rule->points);
        $selected_rule = $rule;
    }
?>
@extends('admin.layouts.app')

@section('content')

<form action="{{route('admin.danger.detail')}}" method="get" name="form1" id="form1">
@csrf
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.danger')}}">危険エリア侵入検知</a></li>
            <li>TOP</li>
        </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">TOP(危険エリア侵入検知)</h2>
            </div>
            <h5>{{date('Y/m/d')}}のデータを表示</h5>
            <div class="title-wrap ver2 stick" style="margin-top: 10px;">
                <div class="sp-ma">
                    <div class="sort">
                        <ul class="date-list">
                            <li>
                                <h4>カメラ</h4>
                            </li>
                            <li><a data-target="camera" class="modal-open setting">選択する</a></li>
                            @if($selected_rule != null)
                                <li><p class="selected-camera">{{$selected_rule->camera_no. '：'. $selected_rule->location_name.'('.$selected_rule->installation_position.')'}}</p></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="list">
                <div class="inner active">
                    <h3 class="title">現在の映像</h3>
                    <div style="display: flex;">
                        <div style="width:50%; position: relative;">
                            <button type="button" class="add-to-toppage" onclick="addToToppage({{config('const.top_block_type_codes')['live_video_danger']}})">ダッシュボートへ追加</button>
                        </div>
                        <div style="width:50%; position: relative;">
                            <button type="button" class="add-to-toppage" onclick="addToToppage({{config('const.top_block_type_codes')['live_graph_danger']}})">ダッシュボートへ追加</button>
                        </div>
                    </div>
                    @if(isset($selected_rule))
                        <div id="image-container" onclick="location.href='{{route('admin.danger.edit', ['danger' => $selected_rule->id])}}'"></div>
                    @endif
                    <div style="display: flex;width:100%;margin-bottom:30px;" class="mainbody">
                        <div class='video-show' style="width:54%;">
                            @if(isset($selected_rule))
                            <div class="streaming-video" style="height:360px;width:640px;">
                                <safie-streaming-player></safie-streaming-player>
                                {{-- <input type="button" value='再生' onClick="play()">
                                <input type="button" value='停止' onClick="pause()"> --}}
                            </div>
                            @endif
                        </div>
                        @if(isset($selected_rule))
                            <canvas id="myLineChart1" onclick="location.href='{{route('admin.danger.edit', ['danger' => $selected_rule->id])}}'"></canvas>
                        @endif
                    </div>
                </div>
            </div>
            <ul class="kenchi-list" style="margin-top: 45px;position: relative;">
                @if(count($danger_detections) > 0)
                    <button type="button" class="add-to-toppage" onclick="addToToppage({{config('const.top_block_type_codes')['recent_detect_danger']}})">ダッシュボートへ追加</button>
                @endif
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
                            <li><a class="move-href" href="{{route("admin.danger.list")}}">検知リスト</a></li>
                        </ul>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!--MODAL -->
    <div id="camera" class="modal-content">
        <div class="textarea">
            <div class="listing">
                <div class="scroll active sp-pl0">
                    <table class="table2 text-centre">
                        <thead>
                        <tr>
                            <th class="w10"></th>
                            <th>カメラNo</th>
                            <th>設置エリア</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
                            <th>カメラ画像確認</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $selected_camera = old('selected_camera', (isset($request) && $request['selected_camera'] > 0)?$request['selected_camera']:null);
                            if ($selected_camera == null && $selected_rule != null){
                                $selected_camera = $selected_rule->camera_id;
                            }
                        ?>
                        @foreach ($cameras as $camera)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    @if ((int)$camera->id == (int)$selected_camera)
                                        <input name="selected_cameras[]" value = '{{$camera->id}}' type="radio" id="{{'camera'.$camera->id}}" checked>
                                    @else
                                        <input name="selected_cameras[]" value = '{{$camera->id}}' type="radio" id="{{'camera'.$camera->id}}">
                                    @endif
                                    <label class="" for="{{'camera'.$camera->id}}"></label>
                                </div>
                            </td>
                            <td>{{$camera->camera_id}}</td>
                            <td>{{$camera->location_name}}</td>
                            <td>{{$camera->floor_number}}</td>
                            <td>{{$camera->installation_position}}</td>
                            <td><img width="100px" src="{{$camera->img}}"/></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="modal-set">
                        <button type="submit" class="modal-close">設 定</button>
                    </div>
                </div>
            </div>
        </div>
        <p class="closemodal"><a class="modal-close">×</a></p>
    </div>
    <!-- -->
</form>
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
<div id="alert-modal" title="test" style="display:none">
    <p><span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">

<style>
    #myLineChart1{
        width:46%!important;
        height: 400px!important;
        cursor: pointer;
    }
    #image-container{
        width:640px;
        height:360px;
        position: absolute;
        z-index: 1;
        cursor: pointer;
    }
    .streaming-video{
        position: absolute;
    }
    .move-href{
        text-decoration: underline;
        color: blue;
        cursor: pointer;
    }
    .add-to-toppage{
        position: absolute;
        right: 0;
        top:-35px;
        padding-left: 5px;
        padding-right:5px;
        padding-top:2px;
        padding-bottom:2px;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>
<script>

    function timeFormat(value){
        var res = value.toString();
        if (value < 10) res = '0' + value.toString();
        return res;
    }

    function videoPlay(path){
        var video = document.getElementById('video-container');
        video.pause();
        $('#video-container').attr('src', path);
        video.play();
    }

    var color_set = {
        1:'red',
        2:'#42b688',
        3:'#42539a',
        4:'black',
    }

    var all_data = <?php echo $all_data;?>;
    var actions = <?php echo json_encode(config('const.action'));?>;

    var now = new Date();
    var min_time = new Date();
    var max_time = new Date();

    max_time.setHours(now.getHours() + 1);
    max_time.setMinutes(0);
    max_time.setSeconds(0);

    min_time.setHours((now.getHours() - 2 < 0 ? 0 : now.getHours() -2));
    min_time.setMinutes(0);
    min_time.setSeconds(0);

    var cur_time = new Date();
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
        var detected_numbers = {};
        Object.keys(actions).map(id => {
            detected_numbers[id] = 0;
        })
        Object.keys(all_data).map(detect_time => {
            var detect_hour = detect_time.split(':')[0];
            var detect_mins = detect_time.split(':')[1];
            var detect_time_object = new Date();
            detect_time_object.setHours(parseInt(detect_hour));
            detect_time_object.setMinutes(parseInt(detect_mins));
            if (detect_time_object.getTime() >= cur_time.getTime() && detect_time_object.getTime() < cur_time.getTime() + 15 * 60 * 1000){
                Object.keys(actions).map(id => {
                    if (all_data[detect_time][id] != undefined){
                        detected_numbers[id]++;
                    }
                })
            }
        })
        date_labels.push(new Date(cur_time));
        cur_time.setMinutes(cur_time.getMinutes() + 15);
        Object.keys(actions).map(id => {
            totals_by_action[id].push(detected_numbers[id]);
            if (detected_numbers[id] > max_y) max_y = detected_numbers[id];
        })
    }

    var datasets = [];
    Object.keys(totals_by_action).map(action_id => {
        datasets.push({
            label:actions[action_id],
            data:totals_by_action[action_id],
            borderColor:color_set[action_id],
            backgroundColor:'white'
        })
    });

    var ctx = document.getElementById("myLineChart1");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: date_labels,
            datasets
        },
        options: {
            legend: {
                labels: {
                    fontSize: 30
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
                fontSize:35,
            },
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMax: max_y + 1,
                        suggestedMin: 0,
                        stepSize: parseInt((max_y + 2)/5) + 1,
                        fontSize: 30,
                        callback: function(value, index, values){
                            return  value +  '回'
                        }
                    }
                }],
                xAxes:[{
                    type: 'time',
                    time: {
                        unit: 'minute',
                        displayFormats: {
                            minute: 'H:mm'
                        },
                        distribution: 'series'
                    },
                    ticks: {
                        fontSize: 30,
                        max: max_time,
                        min: min_time,
                        stepSize: 15,
                    }
                }]
            },
        }
    });

    let safieStreamingPlayerElement;
    let safieStreamingPlayer;
    function load() {
        safieStreamingPlayerElement = document.querySelector('safie-streaming-player');
        if(safieStreamingPlayerElement != undefined && safieStreamingPlayerElement != null){
            safieStreamingPlayer = safieStreamingPlayerElement.instance;
            safieStreamingPlayer.on('error', (error) => {
                console.error(error);
            });
            // 初期化
            safieStreamingPlayer.defaultProperties = {
                defaultAccessToken: '<?php echo $access_token;?>',
                defaultDeviceId: '<?php echo isset($selected_rule) ? $selected_rule->camera_no : '';?>',
                defaultAutoPlay:true
            };
        }
    }
    // function play() {
    //     safieStreamingPlayer.play();
    // }
    // function pause() {
    //     safieStreamingPlayer.pause();
    // }


    var rules = <?php echo json_encode($rules);?>;
    var selected_rule = <?php echo $selected_rule;?>;
    var radius = "<?php echo config('const.camera_mark_radius');?>";
    radius = parseInt(radius);

    var container = document.getElementById('image-container');
    var stage = new Konva.Stage({
        container: 'image-container',
        width: container.clientWidth,
        height: container.clientHeight,
    });
    layer = new Konva.Layer();
    stage.add(layer);

    function isLeft(p0, a, b) {
        return (a.x-p0.x)*(b.y-p0.y) - (b.x-p0.x)*(a.y-p0.y);
    }

    function distCompare(p0, a, b) {
        var distA = (p0.x-a.x)*(p0.x-a.x) + (p0.y-a.y)*(p0.y-a.y);
        var distB = (p0.x-b.x)*(p0.x-b.x) + (p0.y-b.y)*(p0.y-b.y);
        return distA - distB;
    }

    function angleCompare(p0, a, b) {
        var left = isLeft(p0, a, b);
        if (left == 0) return distCompare(p0, a, b);
        return left;
    }
    function sortFigurePoints(figure_points) {

        figure_points = figure_points.splice(0);
        var p0 = {};
        p0.y = Math.min.apply(null, figure_points.map(p=>p.y));
        p0.x = Math.max.apply(null, figure_points.filter(p=>p.y == p0.y).map(p=>p.x));
        figure_points.sort((a,b)=>angleCompare(p0, a, b));
        return figure_points;
    };

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
            strokeWidth: radius - 3 > 0? radius - 3 : 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        layer.add(figure_area);

    }

    $(document).ready(function() {
        if (rules.length > 0){
            rules.map(rule_item => {
                if (rule_item.points != undefined && rule_item.camera_id == selected_rule.camera_id){
                    drawFigure(rule_item.points, rule_item.color);
                }
            })
        }

    });
  </script>
  @endsection
