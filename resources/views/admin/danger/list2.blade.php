@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li>危険エリア侵入検知</li>
        <li>検知リスト</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">検知リスト</h2>
      </div>
      <form action="area_search_result.php" method="post" name="form1" id="form1">
        <div class="title-wrap ver2 stick">
          <div class="sp-ma">
            <div class="sort">
              <ul class="date-list">
                <li>
                  <h4>検出期間</h4>
                </li>
                <li>
                  <input type="date" value="2022-05-01">
                </li>
                <li>～</li>
                <li>
                  <input type="date" value="2022-05-07">
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="list"> 
          
          <!-- .inner end -->
          
          <div class="inner active">
            <ul class="tab_sub">
              <li class="active"><a data-target="rule" class="modal-open blue">ルールから選択</a></li>
              <li><a data-target="camera" class="modal-open blue">カメラから選択</a></li>
              <li><a data-target="action" class="modal-open blue">アクションから選択</a></li>
            </ul>
            <div class="scroll active sp-ma-right"><div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>  <canvas id="myLineChart1" width="1038" height="519" style="display: block; width: 1038px; height: 519px;" class="chartjs-render-monitor"></canvas> </div>
            <div class="scroll sp-ma-right">  <canvas id="myLineChart2" height="0" class="chartjs-render-monitor" style="display: block; width: 0px; height: 0px;" width="0"></canvas></div>
            <div class="scroll sp-ma-right"> <canvas id="myLineChart3" height="0" class="chartjs-render-monitor" style="display: block; width: 0px; height: 0px;" width="0"></canvas> </div>
          </div>
          <!-- .inner end --> 
        </div>
        <!-- .list end --> 
        
        <!--
        <div class="tour-content mt25">
          <div class="float-l pager"><a href="">＜</a><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a><a href="">＞</a></div>
        </div>
-->
        
      </form>
    </div>
  </div>

@endsection
