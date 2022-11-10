@extends('admin.layouts.app')

@section('content')
<?php
    $starttime = (isset($request_params) && isset($request_params['starttime'])) ? date('Y-m-d', strtotime($request_params['starttime'])) : date('Y-m-d');
    $endtime = (isset($request_params) && isset($request_params['endtime'])) ? date('Y-m-d', strtotime($request_params['endtime'])) : date('Y-m-d');
    $search_period = (strtotime($endtime) - strtotime($starttime)) / 86400;
    $selected_rule = old('selected_rule', (isset($request_params) && isset($request_params['selected_rule'])) ? $request_params['selected_rule'] : null);
    $total_data = [];
    $sum = 0;
    foreach ($thief_detections as $item) {
        if (!isset($total_data[date('Y-m-d H:i:00', strtotime($item->starttime))])) {
            $total_data[date('Y-m-d H:i:00', strtotime($item->starttime))] = 0;
        }
        ++$total_data[date('Y-m-d H:i:00', strtotime($item->starttime))];
    }
?>
<form action="{{route('admin.thief.past_analysis')}}" method="get" name="form1" id="form1">
@csrf
    <input type="hidden" name="change_params" value="change"/>
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.thief')}}">大量盗難検知</a></li>
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
                        <h4>検出期間</h4>
                        </li>
                        <li style="width:113px;">
                            <input id='starttime' type="date" name='starttime' onchange="$('#form1').submit()"
                                max="{{strtotime(old('endtime', $endtime)) > strtotime(date('Y-m-d')) ? date('Y-m-d') : old('endtime', $endtime)}}"
                                value="{{ old('starttime', $starttime)}}"/>
                        </li>
                        <li>～</li>
                        <li>
                            <input id='endtime' type="date" name='endtime' onchange="$('#form1').submit()"
                                max="{{date('Y-m-d')}}" min="{{ old('starttime', $starttime)}}" value="{{ old('endtime', $endtime)}}"/>
                        </li>
                    </ul>
                    <ul class="date-list">
                        <li>
                            <h4>ルール</h4>
                        </li>
                        <li><a data-target="camera" class="modal-open setting">選択する</a></li>
                        @if(isset($selected_rule_object))
                            <li><p class="selected-camera">{{$selected_rule_object->name.'('.$selected_rule_object->serial_no.')'. '：'. $selected_rule_object->location_name.'('.$selected_rule_object->installation_position.')'}}</p></li>
                        @endif
                    </ul>
                    </div>
                </div>
            </div>
            <div class="list">
                <div class="inner active">
                    <div style="display: flex; position: relative;">
                        {{-- <h3 class="title">ピット内人数推移</h3> --}}
                        <button type="button" class="add-to-toppage <?php echo $from_top ? 'from_top' : ''; ?>" onclick="addDashboard({{config('const.top_block_type_codes')['past_graph_thief']}})">ダッシュボートへ追加</button>
                    </div>
                    <div class="chart-area" style="position: relative;margin-top:60px;">
                        <div class="period-select-buttons">
                            <?php
                                $time_period = '3';
                                if (isset($request_params['time_period']) && $request_params['time_period'] != '') {
                                    $time_period = $request_params['time_period'];
                                }

                                if ($search_period < 1) {
                                    if (!in_array($time_period, ['3', '6', '12', '24'])) {
                                        $time_period = '3';
                                    }
                                } elseif ($search_period < 7) {
                                    if (!in_array($time_period, ['time', 'day'])) {
                                        $time_period = 'time';
                                    }
                                } elseif ($search_period <= 30) {
                                    if (!in_array($time_period, ['time', 'day'])) {
                                        $time_period = 'time';
                                    }
                                } elseif ($search_period <= 180) {
                                    if (!in_array($time_period, ['day', 'week', 'month'])) {
                                        $time_period = 'day';
                                    }
                                } else {
                                    if (!in_array($time_period, ['day', 'week', 'month'])) {
                                        $time_period = 'day';
                                    }
                                }
                            ?>
                            <input id = 'time_period' type='hidden' name="time_period" value="{{$time_period}}"/>
                            @if ($search_period < 1)
                                <button type="button" class="<?php echo $time_period == '3' ? 'period-button selected' : 'period-button'; ?>"  onclick="displayGraphData(this, '3')">3時間</button>
                                <button type="button" class="<?php echo $time_period == '6' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this,'6')">6時間</button>
                                <button type="button" class="<?php echo $time_period == '12' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this,'12')">12時間</button>
                                <button type="button" class="<?php echo $time_period == '24' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this,'24')">24時間</button>
                            @elseif ($search_period < 7)
                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'time')">時間別</button>
                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'day')">日別</button>
                            @elseif ($search_period <= 30)
                                <button type="button" class="<?php echo $time_period == 'time' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'time')">時間別</button>
                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'day')">日別</button>
                            @elseif ($search_period <= 180)
                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'day')">日別</button>
                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'week')">週別</button>
                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'month')">月別</button>
                            @else
                                <button type="button" class="<?php echo $time_period == 'day' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'day')">日別</button>
                                <button type="button" class="<?php echo $time_period == 'week' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'week')">週別</button>
                                <button type="button" class="<?php echo $time_period == 'month' ? 'period-button selected' : 'period-button'; ?>" onclick="displayGraphData(this, 'month')">月別</button>
                            @endif
                        </div>
                        <a class="prev" onclick="moveXRange(-1)">❮</a>
                        <a class="next" onclick="moveXRange(1)">❯</a>
                        <canvas id="myLineChart1"></canvas>
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
                            <th>ルール名</th>
                            <th>カメラNo</th>
                            <th>設置エリア</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
                            <th>ハンガー</th>
                            <th>カラー</th>
                            <th>ルールの設定期間</th>
                            <th>カメラ画像確認</th>
                            {{-- <th>詳細</th> --}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($rules as $rule)
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap radio-wrap-div">
                                    @if ((int)$rule->id == (int)$selected_rule)
                                        <input name="selected_rule" value = '{{$rule->id}}' type="radio" id="{{'rule'.$rule->id}}" checked>
                                    @else
                                        <input name="selected_rule" value = '{{$rule->id}}' type="radio" id="{{'rule'.$rule->id}}">
                                    @endif
                                    <label class="" for="{{'rule'.$rule->id}}"></label>
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
                            <td><img width="100px" src="{{asset('storage/thumb').'/'.$rule->img_path}}"/></td>
                            {{-- <td><a class="rule-detail-link" onclick="location.href='{{route('admin.thief.rule_view').'?id='.$rule->id}}'">ルール詳細>></a></td> --}}
                        </tr>
                        @endforeach
                        @if(count($rules) == 0)
                        <tr>
                            <td colspan="8">大量盗難検知のルールがありません。ルールを設定してください</td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="modal-set">
                        @if(count($rules) > 0)
                        <button type="submit" class="modal-close">設 定</button>
                        @endif
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
    }
    .period-select-buttons{
        position: absolute;
        right: 10px;
        top: -16px!important;
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
    var ctx = document.getElementById("myLineChart1");
    var search_period = "<?php echo $search_period; ?>";
    var selected_rule = "<?php echo $selected_rule; ?>";
    var starttime = $('#starttime').val();
    starttime = formatDateTime(starttime);
    starttime.setHours(0);
    starttime.setMinutes(0);
    starttime.setSeconds(0);
    var endtime = $('#endtime').val();
    endtime = formatDateTime(endtime);
    endtime.setHours(23);
    endtime.setMinutes(59);
    endtime.setSeconds(59);

    var min_time = new Date(starttime);
    min_time.setHours(0);
    min_time.setMinutes(0);
    min_time.setSeconds(0);
    var max_time = new Date(min_time);

    var grpah_init_type = "<?php echo $time_period; ?>";
    var period_unit = 'hour';
    var displayFormat = {'hour': 'H:mm'};
    var tooltip = "H:mm";
    var grid_unit = 15;
    setGraphOptions(grpah_init_type);

    function setGraphOptions(time_period){
        if (!isNaN(parseInt(time_period))){
            time_period = parseInt(time_period);
        }
        switch(time_period){
            case 3:
                grid_unit = 15;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + parseInt(time_period));
                if (endtime.getMinutes() == 59){
                    endtime.setSeconds(endtime.getSeconds() + 1);
                }
                break;
            case 6:
                grid_unit = 30;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + parseInt(time_period));
                if (endtime.getMinutes() == 59){
                    endtime.setSeconds(endtime.getSeconds() + 1);
                }
                break;
            case 12:
                grid_unit = 60;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + parseInt(time_period));
                if (endtime.getMinutes() == 59){
                    endtime.setSeconds(endtime.getSeconds() + 1);
                }
                break;
            case 24:
                grid_unit = 60;
                period_unit = 'minute';
                displayFormat = {'minute': 'H:mm'};
                tooltip = "H:mm";
                max_time.setHours(max_time.getHours() + parseInt(time_period));
                if (endtime.getMinutes() == 59){
                    endtime.setSeconds(endtime.getSeconds() + 1);
                }
                break;
            case 'time':
                grid_unit = 60;
                period_unit = 'minute';
                displayFormat = {'minute': 'DD日H時'};
                tooltip = "MM/DD H:mm";
                max_time.setDate(max_time.getDate() + 1);
                if (endtime.getMinutes() == 59){
                    endtime.setSeconds(endtime.getSeconds() + 1);
                }
                break;
            case 'day':
                grid_unit = 1;
                period_unit = 'day';
                displayFormat = {'day': 'M/DD'};
                tooltip = "YY/MM/DD";
                max_time.setDate(max_time.getDate() + 7);
                if (endtime.getMinutes() == 0){
                    endtime.setSeconds(endtime.getSeconds() - 1);
                }
                break;
            case 'week':
                grid_unit = 1;
                period_unit = 'week';
                displayFormat = {'week': 'M/DD'};
                tooltip = "YY/MM/DD";
                max_time.setDate(max_time.getDate() + 28);
                if (endtime.getMinutes() == 0){
                    endtime.setSeconds(endtime.getSeconds() - 1);
                }
                break;
            case 'month':
                grid_unit = 1;
                period_unit = 'month';
                displayFormat = {'month': 'YYYY/MM'};
                tooltip = "YY/MM";
                max_time.setMonth(max_time.getMonth() + 6);
                if (endtime.getMinutes() == 0){
                    endtime.setSeconds(endtime.getSeconds() - 1);
                }
                break;
        }
    }

    function moveXRange(increament = 1){
        if (!isNaN(parseInt(grpah_init_type))) grpah_init_type = parseInt(grpah_init_type);
        switch(grpah_init_type){
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
        displayGraphData(null, grpah_init_type, false);
    }

    function resortData(data, time_period){
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
                return data
                break;
        }
        return temp;
    }
    var myLineChart = null;
    var total_data = <?php echo json_encode($total_data); ?>;
    function displayGraphData(e = null, time_period = '3', start_init_flag = true){
        var time_labels = [];
        var y_data = [];
        var point_radius = [];
        grpah_init_type = time_period;
        $('#time_period').val(time_period);
        if (start_init_flag){
            min_time = new Date(starttime);
            min_time.setHours(0);
            min_time.setMinutes(0);
            min_time.setSeconds(0);
        }

        if (e != null){
            $('.period-button').each(function(){
                $(this).removeClass('selected');
            });

            $(e).addClass('selected');
        }
        max_time = new Date(min_time);
        setGraphOptions(time_period);
        if (max_time.getTime() > endtime.getTime()) max_time = new Date(endtime);
        var cur_time = new Date(min_time);
        if (time_period == 'week'){
            var first = cur_time.getDate() - cur_time.getDay();
            cur_time = new Date(cur_time.setDate(first));
        } else if (time_period == 'month'){
            cur_time.setDate(1);
        } else if (time_period == 'day'){
            cur_time.setHours(0);
            cur_time.setMinutes(0);
            cur_time.setSeconds(0);
        }
        var graph_data = resortData(total_data, time_period);
        while(cur_time.getTime() <= max_time.getTime()){
            time_labels.push(new Date(cur_time));
            point_radius.push(0);
            y_data.push(null);

            if (time_period == 'day' || time_period == 'week' || time_period == 'month'){
                var date_key = formatDateLine(cur_time);
                if (time_period == 'week') date_key = formatYearWeekNum(cur_time);
                if (time_period == 'month') date_key = formatYearMonth(cur_time);
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

            switch(time_period){
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
        if (myLineChart != null){
            myLineChart.destroy();
        }
        myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels:time_labels,
                datasets: [{
                    label: '回',
                    data: y_data,
                    borderColor: "#42b688",
                    // backgroundColor: "rgba(66,182,136, 0.3)",
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
                            fontSize: 20,
                            callback: function(value, index, values){
                                return  value +  '回'
                            }
                        }
                    }],
                    xAxes:[{
                        type: 'time',
                        time: {
                            unit: period_unit,
                            tooltipFormat:tooltip,
                            displayFormats:displayFormat,
                            distribution: 'series',
                            stepSize: grid_unit,
                        },
                        ticks: {
                            fontSize: 18,
                            // max: max_time,
                            // min: min_time,
                        }
                    }]
                },
            }
        });

        var search_params = {
            starttime:formatDateLine(new Date($('#starttime').val())),
            endtime:formatDateLine(new Date($('#endtime').val())),
            time_period:grpah_init_type,
            selected_rule:selected_rule
        }
        saveSearchOptions('admin.thief.past_analysis', search_params);
    }
    function addDashboard(block_type){
        var options = {
            starttime:formatDateLine(new Date($('#starttime').val())),
            endtime:formatDateLine(new Date($('#endtime').val())),
            time_period:grpah_init_type,
            selected_rule:selected_rule
        };
        addToToppage(block_type, options);
    }
    $(document).ready(function() {
        displayGraphData(null, grpah_init_type);
        setInterval(() => {
            $.ajax({
                url : '/admin/CheckDetectData',
                method: 'post',
                data: {
                    type:'thief',
                    selected_rule:"<?php echo $selected_rule; ?>",
                    endtime:formatDateLine(new Date($('#endtime').val())),
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    last_record_id : "<?php echo $last_number; ?>"
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
    });
</script>
@endsection
