@extends('admin.layouts.app')

@section('content')
<?php
    $selected_camera = old('selected_camera', (isset($request_params) && isset($request_params['selected_camera']))?$request_params['selected_camera']:null);
    $total_data = array();
    $sum = 0;
?>
<form action="{{route('admin.pit.detail')}}" method="get" name="form1" id="form1">
@csrf
    <input type="hidden" name="change_params" value="change"/>
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.pit')}}">ピット入退場検知</a></li>
            <li>TOP</li>
        </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">TOP(ピット入退場検知)</h2>
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
                    <h3 class="title">ピット内人数推移</h3>
                    @if($selected_rule != null)
                        <div id="image-container" onclick="location.href='{{route('admin.pit.edit', ['pit' => $selected_rule->id])}}'"></div>
                    @endif
                    <div style="display: flex;">
                        <div style="width:50%; position: relative;">
                            <button type="button" class="add-to-toppage <?php echo $from_top?'from_top':'' ?>" onclick="addToToppage({{config('const.top_block_type_codes')['live_video_pit']}})">ダッシュボートへ追加</button>
                        </div>
                        <div style="width:50%; position: relative;">
                            <button type="button" class="add-to-toppage <?php echo $from_top?'from_top':'' ?>" onclick="addToToppage({{config('const.top_block_type_codes')['live_graph_pit']}})">ダッシュボートへ追加</button>
                        </div>
                    </div>
                    <div style="" class="mainbody">
                        <div class='video-show' style="width:54%;">
                            @if($selected_rule != null)
                            <div class="streaming-video" style="height:360px;width:640px;">
                                <safie-streaming-player></safie-streaming-player>
                                {{-- <input type="button" value='再生' onClick="play()">
                                <input type="button" value='停止' onClick="pause()"> --}}
                            </div>
                            @endif
                        </div>
                        @if($selected_rule != null)
                            <div class="period-select-buttons">
                                <?php
                                    $time_period = '3';
                                    if (isset($request_params['time_period']) && $request_params['time_period'] != '') $time_period = $request_params['time_period'];
                                ?>
                                <input id = 'time_period' type='hidden' name="time_period" value="{{$time_period}}"/>
                                <button type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'?>"  onclick="displayGraphData(this, '3')">3時間</button>
                                <button type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'?>"  onclick="displayGraphData(this, '6')">6時間</button>
                                <button type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'?>"  onclick="displayGraphData(this, '12')">12時間</button>
                            </div>
                            <canvas id="myLineChart1" onclick="location.href='{{route('admin.pit.past_analysis')}}'"></canvas>
                        @endif

                    </div>

                    <div class="left-right">
                        <div class="left-box" style="position: relative;">
                            <h3 class="title">ピット内最大時間の超過検知</h3>
                            <button type="button" class="add-to-toppage <?php echo $from_top?'from_top':'' ?>" onclick="addToToppage({{config('const.top_block_type_codes')['recent_detect_pit']}})">ダッシュボートへ追加</button>
                            <table class="table2 text-centre top50">
                                <thead>
                                    <tr>
                                        <th>時間</th>
                                        <th>検知条件</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
                                        <td>9:22</td>
                                        <td>時間オーバー(120)</td>
                                        <td><a class="move-href" href="{{route("admin.pit.list")}}">検知リスト</a></td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="right-box" style="position: relative;">
                            <h3 class="title">入退場履歴</h3>
                            <button type="button" class="add-to-toppage" onclick="addToToppage({{config('const.top_block_type_codes')['pit_history']}})">ダッシュボートへ追加</button>
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
                                    @foreach ($pit_detections as $item)
                                        @if($item->nb_entry != $item->nb_exit)
                                        <?php
                                            $sum += ($item->nb_entry - $item->nb_exit);
                                            $total_data[date('Y-m-d H:i:s', strtotime($item->starttime))] = $sum;
                                        ?>
                                        <tr>
                                            <td>{{date('H:i:s', strtotime($item->starttime))}}</td>
                                            <td>{{$item->nb_entry > $item->nb_exit ? '入場' : '退場'}} </td>
                                            <td><span class="{{$item->nb_entry > $item->nb_exit ? 'f-red' : 'f-blue'}}">{{$item->nb_entry - $item->nb_exit}}</span></td>
                                            <td>{{$sum}}</td>
                                        </tr>
                                        @endif
                                    @endforeach
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
                            <th>設置エリア</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
                            <th>カメラ画像確認</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($cameras as $camera)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap radio-wrap-div">
                                    @if ((int)$camera->id == (int)$selected_camera)
                                        <input name="selected_camera" value = '{{$camera->id}}' type="radio" id="{{'camera'.$camera->id}}" checked>
                                    @else
                                        <input name="selected_camera" value = '{{$camera->id}}' type="radio" id="{{'camera'.$camera->id}}">
                                    @endif
                                    <label class="" for="{{'camera'.$camera->id}}"></label>
                                </div>
                            </td>
                            <td>{{$camera->camera_id}}</td>
                            <td>{{$camera->location_name}}</td>
                            <td>{{$camera->floor_number}}</td>
                            <td>{{$camera->installation_position}}</td>
                            <td><img width="100px" src="{{asset('storage/recent_camera_image/').'/'.$camera->camera_id.'.jpeg'}}"/></td>
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
<div id="alert-modal" title="test" style="display:none">
    <p><span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .mainbody{
        position: relative;
        display: flex;
        width:100%;
        margin-bottom:30px;
    }
    .period-select-buttons{
        position: absolute;
        right: 10px;
        top: -10px;
    }
    .period-select-buttons > button{
        padding:2px;
    }
    .period-select-buttons > .selected{
        background: lightgreen;
    }
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
        top:-50px;
        padding-left: 5px;
        padding-right:5px;
        padding-top:2px;
        padding-bottom:2px;
    }
    .left-box > .add-to-toppage{
        top:0px;
    }
    .right-box > .add-to-toppage{
        top:0px;
    }
    .from_top{
        background: lightblue;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    var ctx = document.getElementById("myLineChart1");
    var time_period = "<?php echo $time_period;?>";
    var selected_camera = "<?php echo $selected_camera;?>";
    var grid_unit = 15;
    var total_data = <?php echo json_encode($total_data);?>;

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
                }]
            },
            options: {
                legend: {
                    labels: {
                        fontSize: 25
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
                            stepSize: 1,
                            fontSize: 25,
                            callback: function(value, index, values){
                                return  value +  '人'
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
                            tooltipFormat:"H:mm",
                            distribution: 'series',
                            stepSize: grid_unit,
                            format:'HH:mm'
                        },
                        ticks: {
                            fontSize: 25,
                            // max: max_time,
                            // min: min_time,
                        }
                    }]
                },

            }
        });

        var search_params = {
            time_period:time_period,
            selected_camera:selected_camera
        }
        saveSearchOptions('admin.pit.detail', search_params);
    }

    function displayGraphData(e = null, x_range = '3'){
        if (e != null){
            $('.period-button').each(function(){
                $(this).removeClass('selected');
            });

            $(e).addClass('selected');
        }
        time_period = x_range;
        $('#time_period').val(time_period);
        x_range = parseInt(x_range);
        switch(x_range){
            case 3:
                grid_unit = 15;
                break;
            case 6:
                grid_unit = 30;
                break;
            case 12:
                grid_unit = 60;
                break;
        }
        var time_labels = [];
        var y_data = [];

        var now = new Date();
        var min_time = new Date();
        var max_time = new Date();

        max_time.setHours(now.getHours() + 1);
        max_time.setMinutes(0);
        max_time.setSeconds(0);

        min_time.setHours((now.getHours() - (x_range - 1) < 0 ? 0 : now.getHours() - (x_range - 1)));
        min_time.setMinutes(0);
        min_time.setSeconds(0);

        var cur_time = new Date();
        cur_time.setHours(min_time.getHours());
        cur_time.setMinutes(0);
        cur_time.setSeconds(0);

        while(cur_time.getTime() <= max_time.getTime()){
            time_labels.push(new Date(cur_time));
            var y_add_flag = false;
            Object.keys(total_data).map((time, index) => {
                if (new Date(time).getTime() >= cur_time.getTime() && new Date(time).getTime() < cur_time.getTime() + grid_unit* 60 * 1000){
                    if (index == 0){
                        y_add_flag = true;
                        if (new Date(time).getTime() != cur_time.getTime()) {
                            time_labels.push(new Date(time));
                            y_data.push(null);
                        }
                    } else {
                        time_labels.push(new Date(time));
                    }
                    y_data.push(total_data[time]);
                }
            })
            if (y_add_flag == false){
                y_data.push(null);
            }
            cur_time.setMinutes(cur_time.getMinutes() + grid_unit);
        }

        drawGraph(time_labels, y_data);
    }

    $(document).ready(function() {
        displayGraphData(null, time_period);
    })
</script>
<script src="{{ asset('assets/admin/js/konva.js?2') }}"></script>
<script src="https://swc.safie.link/latest/" onLoad="load()" defer></script>

<script>
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
                defaultDeviceId: '<?php echo isset($selected_rule) ? $selected_rule->camera_no : '';?>',
                defaultAutoPlay:true,
                defaultUserInteractions:false
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
        var selected_rule = <?php echo $selected_rule;?>;
        if (selected_rule != null){
            drawing(selected_rule);
        }

    });
</script>

@endsection
