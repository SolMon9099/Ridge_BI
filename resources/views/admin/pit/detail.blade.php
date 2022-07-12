@extends('admin.layouts.app')

@section('content')

<form action="{{route('admin.pit.detail')}}" method="get" name="form1" id="form1">
@csrf
    <div id="wrapper">
        <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.pit')}}">ピット入退場検知</a></li>
            <li>詳細分析</li>
        </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">詳細分析</h2>
            </div>
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                    <div class="sort">
                    <ul class="date-list">
                        <li>
                        <h4>検出期間</h4>
                        </li>
                        <li>
                            <input id='starttime' type="date" name='starttime' value="{{ old('starttime', (isset($request) && $request->has('starttime'))?$request->starttime:date('Y-m-01'))}}">
                        </li>
                        <li>～</li>
                        <li>
                            <input id='endtime' type="date" name='endtime' value="{{ old('endtime', (isset($request) && $request->has('endtime'))?$request->endtime:date('Y-m-t'))}}">
                        </li>
                    </ul>
                    <ul class="date-list">
                        <li>
                            <h4>カメラ</h4>
                        </li>
                        <li><a data-target="camera" class="modal-open setting">選択する</a></li>
                        <li><p></p></li>
                    </ul>
                    <button type="button" class="apply">レポート出力</button>
                    </div>
                </div>
            </div>
            <div class="list">
                <div class="inner active">
                    <h3 class="title">ピット内人数推移</h3>
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
                            <h3 class="title">アラート検知</h3>
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
                                        <td>人数オーバー(2人)</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script>
    var ctx = document.getElementById("myLineChart1");
    var time_labels = ['09:05:25', '09:22:12', '11:23:17', '12:33:41', '14:25:32', '15:31:45', '18:23:14', '19:47:51'];
    for(var i = 0; i<time_labels.length; i++){
        time_labels[i] = new Date('2022-07-12 ' + time_labels[i]);
    }
    time_labels.unshift(new Date('2022-07-12 08:00:00'));
    time_labels.push(new Date('2022-07-12 20:00:00'));
    var y_data = [1,2,1,0,1,0,1,0];
    y_data.unshift(null);
    y_data.push(null);
    var min_time = new Date();
    min_time.setHours(8);
    min_time.setMinutes(0);
    min_time.setSeconds(0);
    var max_time = new Date();
    max_time.setHours(20);
    max_time.setMinutes(0);
    max_time.setSeconds(0);
    console.log('min', min_time, max_time);
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            // labels: ['0:00', '3:00', '6:00', '9:00', '12:00', '15:00', '18:00', '21:00','24:00'],
            labels:time_labels,
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
            // plugins: {
            //     title: {
            //         display: true,
            //         text: (ctx) => 'Step ' + ctx.chart.data.datasets[0].stepped + ' Interpolation',
            //     }
            // },
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
</script>
@endsection
