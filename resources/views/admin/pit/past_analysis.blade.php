@extends('admin.layouts.app')

@section('content')
<?php
    $total_data = array();
    $sum = 0;
    foreach ($pit_detections as $item) {
        $sum += ($item->nb_entry - $item->nb_exit);
        $total_data[date('Y-m-d H:i:s', strtotime($item->starttime))] = $sum;
    }
?>
<form action="{{route('admin.pit.past_analysis')}}" method="get" name="form1" id="form1">
@csrf
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.pit')}}">ピット入退場検知</a></li>
            <li>過去グラフ</li>
        </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">過去グラフ</h2>
            </div>
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                    <ul class="date-list">
                        <li>
                        <h4>検出日</h4>
                        </li>
                        <li>
                            <input id='searchdate' type="date" name='searchdate' onchange="search()"
                                value="{{ old('searchdate', (isset($request) && $request->has('searchdate'))?$request->searchdate:date('Y-m-d'))}}">
                        </li>
                    </ul>
                    <ul class="date-list">
                        <li>
                            <h4>カメラ</h4>
                        </li>
                        <li><a data-target="camera" class="modal-open setting">選択する</a></li>
                        @if(isset($selected_rule))
                            <li><p class="selected-camera">{{$selected_rule->camera_no. '：'. $selected_rule->location_name.'('.$selected_rule->installation_position.')'}}</p></li>
                        @endif
                    </ul>
                    </div>
                </div>
            </div>
            <div class="list">
                <div class="inner active" style="position: relative;">
                    <div style="display: flex; position: relative;">
                        <h3 class="title">ピット内人数推移</h3>
                        <button type="button" class="add-to-toppage <?php echo $from_top?'from_top':'' ?>" onclick="addToToppage({{config('const.top_block_type_codes')['past_graph_pit']}})">ダッシュボートへ追加</button>
                    </div>
                    <div class="period-select-buttons">
                        <button type="button" class="period-button three selected" onclick="displayGraphData(3)">3時間</button>
                        <button type="button" class="period-button six" onclick="displayGraphData(6)">6時間</button>
                        <button type="button" class="period-button twelve" onclick="displayGraphData(12)">12時間</button>
                        <button type="button" class="period-button twofour" onclick="displayGraphData(24)">24時間</button>
                    </div>
                    <a class="prev" onclick="moveXRange(-1)">❮</a>
                    <a class="next" onclick="moveXRange(1)">❯</a>
                    <canvas id="myLineChart1"></canvas>

                    <div class="left-right">
                        <div class="left-box">
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
                        <div class="right-box">
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
                        <?php
                            $selected_camera = old('selected_camera', (isset($request) && $request['selected_camera'] > 0)?$request['selected_camera']:null);
                        ?>
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
<div id="alert-modal" title="test" style="display:none">
    <p><span id="confirm_text">These items will be permanently deleted and cannot be recovered. Are you sure?</span></p>
</div>
<link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .time-change-btn{
        margin-left: 25px;
        height: 35px;
        margin-top: 10px;
        padding-left: 10px;
        padding-right:10px;
    }
    .add-to-toppage{
        position: absolute;
        right:0px;
        top:0px;
        padding-left: 5px;
        padding-right:5px;
        padding-top:2px;
        padding-bottom:2px;
    }
    .period-select-buttons{
        position: absolute;
        right: 10px;
        top: 45px;
    }
    .period-select-buttons > button{
        padding:3px;
    }
    .period-select-buttons > .selected{
        background: lightgreen;
    }
    .prev, .next {
        cursor: pointer;
        position: absolute;
        top: 25%;
        width: auto;
        padding-left: 15px;
        padding-right: 15px;
        padding-top:8px;
        padding-bottom: 8px;
        font-weight: bold;
        font-size: 18px;
        transition: 0.6s ease;
        /* border-radius: 0 3px 3px 0; */
        user-select: none;
    }
    .prev{
        left:-20px;
    }
    .next {
        right: -20px;
        /* border-radius: 3px 0 0 3px; */
    }

    .prev:hover, .next:hover {
        background-color:lightcoral;
        color:white;
        border-radius: 20px;
    }
    .from_top{
        background: lightblue;
    }
    /* #myLineChart1{
        width:50%!important;
        height: 360px!important;
    } */
    /* #image-container{
        width:640px;
        height:360px;
        position: absolute;
    } */
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script>
    function search(){
        $('#form1').submit();
    }
    var x_range = 3;
    var start_x = 0;

    var ctx = document.getElementById("myLineChart1");
    var total_data = <?php echo json_encode($total_data);?>;

    // var time_labels = ['09:05:25', '09:22:12', '11:23:17', '12:33:41', '14:25:32', '15:31:45', '18:23:14', '19:47:51'];
    // var y_data = [1,2,1,0,1,0,1,0];
    // for(var i = 0; i<time_labels.length; i++){
    //     time_labels[i] = new Date('2022-07-12 ' + time_labels[i]);
    // }
    // time_labels.unshift(new Date('2022-07-12 08:00:00'));
    // time_labels.push(new Date('2022-07-12 20:00:00'));
    // y_data.unshift(null);
    // y_data.push(null);

    function drawGraph(x_data, y_data, grid_unit){
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
                legend: {
                    labels: {
                        fontSize: 20
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
                            fontSize: 20,
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
                            fontSize: 15,
                            // max: max_time,
                            // min: min_time,
                        }
                    }]
                },

            }
        });
    }

    function moveXRange(increament = 1){
        if (x_range == 24) return;
        if (increament == 1){
            start_x = start_x + x_range;
            if (start_x >= 24) start_x = 0;
        } else {
            start_x = start_x - x_range;
            if (start_x < 0) start_x += 24;
        }

        displayGraphData(x_range, false);
    }

    function displayGraphData(time_period = 3, start_x_init = true){
        var grid_unit = 15;
        x_range = time_period;
        if (start_x_init) start_x = 0;
        $('.period-button').each(function(){
            $(this).removeClass('selected');
        });
        switch(time_period){
            case 3:
                $('.three').addClass('selected');
                grid_unit = 15;
                break;
            case 6:
                $('.six').addClass('selected');
                grid_unit = 30;
                break;
            case 12:
                $('.twelve').addClass('selected');
                grid_unit = 60;
                break;
            case 24:
                $('.twofour').addClass('selected');
                grid_unit = 60;
                break;
        }
        var search_date = $('#searchdate').val();
        var min_time = new Date(search_date);
        min_time.setHours(start_x);
        min_time.setMinutes(0);
        min_time.setSeconds(0);
        var max_time = new Date(search_date);
        max_time.setHours(start_x + time_period);
        max_time.setMinutes(0);
        max_time.setSeconds(0);

        var cur_time = new Date(search_date);
        cur_time.setHours(min_time.getHours());
        cur_time.setMinutes(0);
        cur_time.setSeconds(0);

        var time_labels = [];
        var y_data = [];

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

        drawGraph(time_labels, y_data, grid_unit);
    }
    $(document).ready(function() {
        displayGraphData();
    });
</script>
@endsection
