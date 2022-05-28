@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
  <li>棚乱れ検知</li>
        <li>ルール一覧</li>
							<li>ルール編集</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">ルール編集</h2>
      </div>
      <div class="flow">
        <ul>
          <li><span>Step.1</span>カメラを選択</li>
          <li class="active"><span>Step.2</span>エリアを選択</li>
        </ul>
      </div>
      <div class="title-wrap sp-m">
        <button type="button" class="edit left">＋ ルール追加</button>
      </div>
      <form action="shelf_area.php" method="post" name="form1" id="form1">
        <div class="scroll">
          <table class="table2 text-centre">
            <thead>
              <tr>
                <th>カラー</th>
                <th>エリア選択</th>
															<th>削除</th>
              </tr>
            </thead>
            <tbody>
              <tr>
              
                <td><input type="color" id="color1" name="color1" value="#C00000"></td>
                <td><button type="button" class="edit play-video" data-id="1">エリア選択</button></td>
																	<td><button type="button" class="history">削除</button></td>
              </tr>
              <tr>
             
                <td><input type="color" id="color2" name="color2" value="#2CC30E"></td>
                <td><button type="button" class="edit play-video" data-id="2">エリア選択</button></td>
																	<td><button type="button" class="history">削除</button></td>
              </tr>
              <tr>
            
                <td><input type="color" id="color3" name="color3" value="#FFE100"></td>
                <td><button type="button" class="edit play-video" data-id="3">エリア選択</button></td>
																	<td><button type="button" class="history">削除</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="video-area">
          <div id="canvas-container" style="background: url(common/img/canvas2.jpg) no-repeat;	background-size:100%;">
            <canvas id="c" onclick="drawSquare(event)" onmousemove="mouseMove(event)"></canvas>
          </div>
          <div id="debug"></div>
        </div>
        <div class="btns" id="direction">
          <button type="submit" class="ok">決定</button>
        </div>
	<input type="hidden" id="is_rule_number" name="is_rule_number">
      </form>
    </div>
  </div>

@endsection
