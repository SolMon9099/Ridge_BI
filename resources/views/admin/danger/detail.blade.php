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
            <li>ダッシュボード</li>
        </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">ダッシュボード({{date('Y/m/d')}})</h2>
            </div>
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                        <ul class="date-list">
                            <li>
                                <h4>カメラ</h4>
                            </li>
                            <li><a data-target="camera" class="modal-open setting">選択する</a></li>
                            <li><p></p></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="list">
                <div class="inner active">
                    <h3 class="title">現在の映像</h3>
                    @if(isset($selected_rule))
                        <div id="image-container" onclick="location.href='{{route('admin.danger.edit', ['danger' => $selected_rule->id])}}'"></div>
                    @endif
                    <div style="display: flex;width:100%;margin-bottom:30px;" class="mainbody">
                        <div class='video-show' style="width:50%;">
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
            <ul class="kenchi-list" style="margin-top: 45px;">
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
                    </div>
                    <div class="text">
                        <time>{{date('Y/m/d H:i', strtotime($item->starttime))}}</time>
                        <table>
                        <tr>
                            <td>{{$item->location_name}}</td>
                            <td>{{$item->floor_number}}</td>
                            <td>{{$item->installation_position}}</td>
                            <td style="width:10%;">
                                @if (isset($item->action_id))
                                @foreach (json_decode($item->action_id) as $action_code)
                                    <div>{{config('const.action')[$action_code]}}</div>
                                @endforeach
                                @endif
                            </td>
                            <td><a class="move-href">検知リスト</a></td>
                        </tr>
                        </table>
                    </div>
                </li>
                @endforeach
                {{-- <li>
                    <div class="movie"><a data-target="movie0000" class="modal-open  setting2 play"><img src="{{ asset('assets/admin/img/samplepic.svg') }}"></a></div>
                    <div class="text">
                        <time>2022/8/9 11:00</time>
                        <table>
                            <tr>
                                <td>（仮称）ＧＳプロジェクト新築工事</td>
                                <td>3階</td>
                                <td>トイレ横の資材置き場</td>
                                <td style="width:10%;">侵入する</td>
                                <td><a class="move-href">検知リスト</a></td>
                            </tr>
                        </table>
                    </div>
                </li> --}}
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
                            $selected_camera_ids = old('selected_cameras', (isset($request) && $request->has('selected_cameras'))?$request->selected_cameras:[]);
                        ?>
                        @foreach ($cameras as $camera)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    @if (in_array($camera->id, $selected_camera_ids))
                                        <input name="selected_cameras[]" value = '{{$camera->id}}' type="checkbox" id="{{'camera'.$camera->id}}}}" checked>
                                    @else
                                        <input name="selected_cameras[]" value = '{{$camera->id}}' type="checkbox" id="{{'camera'.$camera->id}}}}">
                                    @endif
                                    <label class="custom-style" for="{{'camera'.$camera->id}}}}"></label>
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
<style>
    #myLineChart1{
        width:50%!important;
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
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>
<script>

    function search(){
        $('#form1').submit();
    }

    function setSelectedSearchOption(value){
        $('#selected_search_option').val(value);
    }

    var all_data = <?php echo $all_data;?>;
    var actions = <?php echo json_encode(config('const.action'));?>;
    var date_labels = [];
    var totals_by_action = {};
    var color_set = {
        1:'red',
        2:'#42b688',
        3:'#42539a',
        4:'black',
    }
    Object.keys(actions).map(id => {
        totals_by_action[id] = [];
    })
    var max_y = 0;

    var date_key = new Date().getFullYear();
    var month = new Date().getMonth() + 1 > 9 ? (new Date().getMonth() + 1).toString(): '0' + (new Date().getMonth() + 1).toString();
    var date = new Date().getDate() > 9 ? (new Date().getDate()).toString(): '0' + (new Date().getDate()).toString();
    date_key += '-' + month + '-' + date;
    var month_date_label = (new Date().getMonth() + 1).toString() + '/' + new Date().getDate();
    date_labels.push(month_date_label);
    if (all_data[date_key] == undefined){
        Object.keys(actions).map(id => {
            totals_by_action[id].push(0);
        })
    } else {
        Object.keys(actions).map(id => {
            if (all_data[date_key][id] == undefined){
                totals_by_action[id].push(0);
            } else {
                totals_by_action[id].push(all_data[date_key][id].length);
                if (all_data[date_key][id].length > max_y) max_y = all_data[date_key][id].length;
            }
        })
    }
    console.log('total', totals_by_action);

    var datasets = [];
    Object.keys(totals_by_action).map(action_id => {
        datasets.push({
            label:actions[action_id],
            data:totals_by_action[action_id],
            borderColor:color_set[action_id],
            backgroundColor:'white'
        })
    });
    console.log('datasets', datasets);

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
            title: {
                display: true,
                text: 'NGアクション毎の回数',
                fontSize:35,
            },
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMax: max_y + 2,
                        suggestedMin: 0,
                        stepSize: 1,
                        fontSize: 30,
                        callback: function(value, index, values){
                        return  value +  '回'
                        }
                    }
                }],
                xAxes:[{
                    // type: 'time',
                    // time: {
                    //     unit: 'hour',
                    //     displayFormats: {
                    //         hour: 'H:mm'
                    //     },
                    //     distribution: 'series'
                    // },
                    ticks: {
                        fontSize: 30,
                        // max: max_time,
                        // min: min_time,
                        // stepSize: 1,
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
