@extends('admin.layouts.app')

@section('content')
<?php
    foreach ($rules as $rule) {
        if ($rule->red_points != null && $rule->red_points != '') $rule->red_points = json_decode($rule->red_points);
        if ($rule->blue_points != null && $rule->blue_points != '') $rule->blue_points = json_decode($rule->blue_points);
    }
?>
<form action="{{route('admin.pit.detail')}}" method="get" name="form1" id="form1">
@csrf
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.pit')}}">ピット入退場検知</a></li>
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
                    <h3 class="title">ピット内人数推移</h3>
                    <div id="image-container"></div>
                    <div style="display: flex;width:100%;margin-bottom:30px;" class="mainbody">
                        <div class='video-show' style="width:50%;">
                            <div class="streaming-video" style="height:360px;width:640px;">
                                <safie-streaming-player></safie-streaming-player>
                                <input type="button" value='再生' onClick="play()">
                                <input type="button" value='停止' onClick="pause()">
                            </div>
                        </div>
                        <canvas id="myLineChart1"></canvas>
                    </div>

                    <div class="left-right">
                        <div class="left-box">
                            <h3 class="title">ピット内最大時間の超過検知</h3>
                            <table class="table2 text-centre top50">
                                <thead>
                                    <tr>
                                        <th>時間</th>
                                        <th>検知条件</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>9:22</td>
                                        <td>時間オーバー(120)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="right-box">
                            <h3 class="title">入退場履歴</h3>
                            <table class="table2 text-centre top50">
                                <thead>
                                    <tr>
                                        <th>時間</th>
                                        <th>検知条件</th>
                                        <th>人数変化</th>
                                        <th>ピット内人数</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>9:05:25</td>
                                        <td>入場</td>
                                        <td><span class="f-red">+1</span></td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>9:22:12</td>
                                        <td>入場</td>
                                        <td><span class="f-red">+1</span></td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>11:23:17</td>
                                        <td>退場</td>
                                        <td><span class="f-blue">-1</span></td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>12:33:41</td>
                                        <td>退場</td>
                                        <td><span class="f-blue">-1</span></td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>14:25:32</td>
                                        <td>入場</td>
                                        <td><span class="f-blue">+1</span></td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>15:31:45</td>
                                        <td>退場</td>
                                        <td><span class="f-blue">-1</span></td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>18:23:14</td>
                                        <td>入場</td>
                                        <td><span class="f-blue">+1</span></td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>19:47:51</td>
                                        <td>退場</td>
                                        <td><span class="f-blue">-1</span></td>
                                        <td>0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
                            <th>現場名</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
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
        height: 360px!important;
    }
    #image-container{
        width:640px;
        height:360px;
        position: absolute;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script>
    var ctx = document.getElementById("myLineChart1");
    var time_labels = ['09:05:25', '09:22:12', '11:23:17', '12:33:41', '14:25:32', '15:31:45', '18:23:14', '19:47:51'];
    var y_data = [1,2,1,0,1,0,1,0];
    for(var i = 0; i<time_labels.length; i++){
        time_labels[i] = new Date('2022-07-12 ' + time_labels[i]);
    }
    time_labels.unshift(new Date('2022-07-12 08:00:00'));
    time_labels.push(new Date('2022-07-12 20:00:00'));
    y_data.unshift(null);
    y_data.push(null);
    // var min_time = new Date();
    // min_time.setHours(8);
    // min_time.setMinutes(0);
    // min_time.setSeconds(0);
    // var max_time = new Date();
    // max_time.setHours(20);
    // max_time.setMinutes(0);
    // max_time.setSeconds(0);

    function drawGraph(x_data, y_data){
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels:x_data,
                datasets: [{
                    label: '人',
                    steppedLine:true,
                    data: y_data,
                    borderColor: "#42b688",
                    backgroundColor: "rgba(66,182,136, 0.3)",
                    pointBackgroundColor:'red',
                    fill:true
                }],
            },
            options: {
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
                            stepSize: 1,
                            callback: function(value, index, values){
                                return  value +  '人'
                            }
                        }
                    }],
                    xAxes:[{
                        type: 'time',
                        time: {
                            unit: 'hour',
                            displayFormats: {
                                hour: 'H:mm'
                            },
                            distribution: 'series'
                        },
                        // ticks: {
                        //     max: max_time,
                        //     min: min_time,
                        //     stepSize: 1,
                        // }
                    }]
                },

            }
        });
    }

    drawGraph(time_labels, y_data);
    setInterval(() => {
        for (var i = 0; i < y_data.length; i++){
            y_data[i] = Math.floor(Math.random() * 5);
        }
        drawGraph(time_labels, y_data);
    }, 100000);
</script>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>

<script>
    var rules = <?php echo json_encode($rules);?>;
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

    function drawCircle(center_point, point_index, selected_color = null){
        var circle = new Konva.Circle({
            x: center_point.x,
            y: center_point.y,
            radius: radius,
            fill: selected_color != null ? selected_color : 'red',
            stroke: selected_color != null ? selected_color : 'red',
            strokeWidth: 1,
            id:point_index
        });
        layer.
        add(circle);
    }
    function is_cross(line1, line2){
        var a = line1[0]; // A point
        var b = line1[1]; // B point
        var c = line2[0]; // C point
        var d = line2[1]; // D point
        let s = (b.x - a.x) * (c.y - a.y) - (c.x - a.x) * (b.y - a.y);
        let t = (b.x - a.x) * (d.y - a.y) - (d.x - a.x) * (b.y - a.y);
        return s * t < 0;
    }
    function sortRectanglePoints(rect_points) {
        var res = rect_points;
        if (is_cross([rect_points[0], rect_points[1]], [rect_points[2], rect_points[3]])) {
            res = [rect_points[0], rect_points[2], rect_points[1], rect_points[3]];
        } else if (is_cross([rect_points[0], rect_points[2]], [rect_points[1], rect_points[3]])) {
            res = [rect_points[0], rect_points[1], rect_points[2], rect_points[3]];
        } else {
            res = [rect_points[0], rect_points[1], rect_points[3], rect_points[2]];
        }
        return res;
    }
    function drawRect(rect_points, selected_color = null){
        // layer.find('Line').map(line_item => {
        //     line_item.destroy();
        // })
        // layer.draw();
        rect_points = sortRectanglePoints(rect_points);
        var rect_area = new Konva.Line({
            points: [
                rect_points[0].x/2, rect_points[0].y/2,
                rect_points[1].x/2, rect_points[1].y/2,
                rect_points[2].x/2, rect_points[2].y/2,
                rect_points[3].x/2, rect_points[3].y/2,
                rect_points[0].x/2, rect_points[0].y/2
            ],
            stroke: selected_color != null ? selected_color : 'red',
            strokeWidth: radius - 3 > 0? radius - 3 : 2,
            lineCap: 'round',
            lineJoin: 'round',
        });
        layer.add(rect_area);
    }
    function drawing(rule){
        if (rule.red_points != undefined){
            drawRect(rule.red_points, 'red');
        }
        if (rule.blue_points != undefined){
            drawRect(rule.blue_points, 'blue');
        }
    }
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
                defaultDeviceId: '<?php echo $device_id;?>',
                defaultAutoPlay:true
            };
        }
    }
    function play() {
        safieStreamingPlayer.play();
    }
    function pause() {
        safieStreamingPlayer.pause();
    }

    $(document).ready(function() {
        if (rules.length > 0){
            drawing(rules[0]);
        }

    });
</script>

@endsection
