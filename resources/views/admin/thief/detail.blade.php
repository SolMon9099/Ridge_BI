@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li>大量盗難検知</li>
            <li>詳細分析</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">詳細分析</h2>
        </div>
        <form action="" method="post" name="form1" id="form1">
            <div class="title-wrap ver2 stick">
            <div class="sp-ma">
                <div class="sort">
                <ul class="date-list">
                    <li>
                    <h4>検出期間</h4>
                    </li>
                    <li>
                        <input type="date" value="<?php echo date('Y-m-d');?>">
                    </li>
                    <li>～</li>
                    <li>
                        <input type="date" value="<?php echo date('Y-m-d');?>">
                    </li>
                </ul>
                </div>
            </div>
            </div>
            <div class="list">
                <div class="inner active">
                    <ul class="tab_sub">
                        <li class="active"><a data-target="rule" class="modal-open blue">ルールから選択</a></li>
                        <li><a data-target="camera" class="modal-open blue">カメラから選択</a></li>
                    </ul>
                    <div class="scroll active sp-ma-right">  <canvas id="myLineChart1"></canvas></div>
                    <div class="scroll sp-ma-right">  <canvas id="myLineChart2"></canvas></div>
                </div>
            </div>
        </form>
    </div>
</div>

<!--MODAL -->
<div id="rule" class="modal-content">
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
                        <th>アクション</th>
                        <th>カラー</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="stick-t"><div class="checkbtn-wrap">
                            <input name="checkbox" type="checkbox" id="1">
                            <label for="1"></label>
                        </div></td>
                        <td> 12345</td>
                        <td>（仮称）ＧＳプロジェクト新築工事</td>
                        <td>3階</td>
                        <td>トイレ横の資材置き場</td>
                        <td>横たわる</td>
                        <td><input type="color" id="color1" name="color1" value="#C00000" disabled></td>
                    </tr>
                    <tr>
                        <td class="stick-t"><div class="checkbtn-wrap">
                            <input name="checkbox" type="checkbox" id="2">
                            <label for="2"></label>
                        </div></td>
                        <td> 12345</td>
                        <td>（仮称）ＧＳプロジェクト新築工事</td>
                        <td>3階</td>
                        <td>トイレ横の資材置き場</td>
                        <td>侵入する</td>
                        <td><input type="color" id="color2" name="color2" value="#2CC30E" disabled></td>
                    </tr>
                    <tr>
                        <td class="stick-t"><div class="checkbtn-wrap">
                            <input name="checkbox" type="checkbox" id="3">
                            <label for="3"></label>
                        </div></td>
                        <td> 12345</td>
                        <td>（仮称）ＧＳプロジェクト新築工事</td>
                        <td>3階</td>
                        <td>トイレ横の資材置き場</td>
                        <td>寄りかかる</td>
                        <td><input type="color" id="color3" name="color3" value="#FFE100" disabled></td>
                    </tr>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="stick-t"><div class="checkbtn-wrap">
                                <input name="checkbox" type="checkbox" id="4">
                                <label for="4"></label>
                                </div></td>
                            <td> 12345</td>
                            <td>（仮称）ＧＳプロジェクト新築工事</td>
                            <td>3階</td>
                            <td>トイレ横の資材置き場</td>
                        </tr>
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    <input name="checkbox" type="checkbox" id="5">
                                    <label for="5"></label>
                                </div>
                            </td>
                            <td> 12345</td>
                            <td>（仮称）ＧＳプロジェクト新築工事</td>
                            <td>3階</td>
                            <td>トイレ横の資材置き場</td>
                        </tr>
                        <tr>
                            <td class="stick-t">
                                <div class="checkbtn-wrap">
                                    <input name="checkbox" type="checkbox" id="6">
                                    <label for="6"></label>
                                </div>
                            </td>
                            <td> 12345</td>
                            <td>（仮称）ＧＳプロジェクト新築工事</td>
                            <td>3階</td>
                            <td>トイレ横の資材置き場</td>
                        </tr>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script>
    var ctx = document.getElementById("myLineChart1");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['5/1', '5/2', '5/3', '5/4', '5/5', '5/6', '5/7'],
            datasets: [
            {
                label: 'A',
                data: [35, 34, 37, 35, 34, 35, 34, 25],
                borderColor: "#42b688",
                backgroundColor: "rgba(0,0,0,0)"
            },
            {
                label: 'B',
                data: [25, 27, 27, 25, 26, 27, 25, 21],
                borderColor: "#42539a",
                backgroundColor: "rgba(0,0,0,0)"
            },
                                {
                label: 'C',
                data: [5, 7, 7, 5, 6, 7, 5, 21],
                borderColor: "#448b95",
                backgroundColor: "rgba(0,0,0,0)"
            }
            ],
        },
        options: {
            title: {
                display: true,
                text: '大量盗難検知の合計回数'
            },
            scales: {
                yAxes: [{
                    ticks: {
                    suggestedMax: 40,
                    suggestedMin: 0,
                    stepSize: 10,
                    callback: function(value, index, values){
                        return  value +  '回'
                    }
                    }
                }]
            },
        }
    });

    var ctx2 = document.getElementById("myLineChart2");
    var myLineChart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['5/1', '5/2', '5/3', '5/4', '5/5', '5/6', '5/7'],
            datasets: [
            {
                label: 'A',
                data: [35, 34, 37, 35, 34, 35, 34, 25],
                borderColor: "#42b688",
                backgroundColor: "rgba(0,0,0,0)"
            },
            {
                label: 'B',
                data: [25, 27, 27, 25, 26, 27, 25, 21],
                borderColor: "#42539a",
                backgroundColor: "rgba(0,0,0,0)"
            }
            ],
        },
        options: {
            title: {
                display: true,
                text: '大量盗難検知の合計回数'
            },
            scales: {
                yAxes: [{
                    ticks: {
                    suggestedMax: 40,
                    suggestedMin: 0,
                    stepSize: 10,
                    callback: function(value, index, values){
                        return  value +  '回'
                    }
                    }
                }]
            },
        }
    });
</script>
@endsection
