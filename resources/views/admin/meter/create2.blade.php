@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li>危険エリア侵入検知</li>
        <li>ルール一覧・編集</li>
        <li>ルール新規作成</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">ルール新規作成</h2>
      </div>
      <div class="flow">
        <ul>
          <li><span>Step.1</span>カメラを選択</li>
          <li class="active"><span>Step.2</span>エリアとしきい値を設定</li>
        </ul>
      </div>
      <div class="title-wrap sp-m">
        <button type="button" class="edit left">＋ ルール追加</button>
      </div>
      <form action="meter_area.php" method="post" name="form1" id="form1">
        <div class="scroll">
          <table class="table2 text-centre">
            <thead>
              <tr>
                <th>カラー</th>
                <th>メーター種別</th>
                <th class="w25">範囲</th>
                <th class="w10">値1</th>
                <th class="w10">値2</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="color" id="color1" name="color1"></td>
                <td><div class="select-c">
                    <select class="extraction">
                      <option value="数値タイプ" selected="">数値タイプ</option>
                      <option value="指針タイプ">指針タイプ</option>
                    </select>
                  </div></td>
                <td><div class="num">
                    <div class="select-c">
                      <select>
                        <option>次の値の間</option>
                        <option>次の値の間</option>
                        <option>次の値より大きい</option>
                        <option>次の値より小さい</option>
                      </select>
                    </div>
                  </div></td>
                <td><div class="num">
                    <input type="number">
                  </div></td>
                <td><div class="num">
                    <input type="number">
                  </div></td>
                <td><button type="button" class="edit play-video" data-id="1">エリア選択</button></td>
              </tr>
              <tr>
                <td><input type="color" id="color2" name="color2"></td>
                <td><div class="select-c">
                    <select class="extraction">
                      <option value="数値タイプ">数値タイプ</option>
                      <option value="指針タイプ" selected="">指針タイプ</option>
                    </select>
                  </div></td>
                <td><div class="guide">
                    <div class="select-c">
                      <select>
                        <option>範囲内</option>
                        <option>範囲外</option>
                      </select>
                    </div>
                  </div></td>
                <td></td>
                <td></td>
                <td><button type="button" class="edit play-video" data-id="1">エリア選択</button></td>
              </tr>
														  <tr>
                <td><input type="color" id="color3" name="color3"></td>
                <td><div class="select-c">
                    <select class="extraction">
                      <option value="数値タイプ" selected="">数値タイプ</option>
                      <option value="指針タイプ">指針タイプ</option>
                    </select>
                  </div></td>
                <td><div class="num">
                    <div class="select-c">
                      <select>
                        <option>次の値の間</option>
                        <option>次の値の間</option>
                        <option>次の値より大きい</option>
                        <option>次の値より小さい</option>
                      </select>
                    </div>
                  </div></td>
                <td><div class="num">
                    <input type="number">
                  </div></td>
                <td><div class="num">
                    <input type="number">
                  </div></td>
                <td><button type="button" class="edit play-video" data-id="1">エリア選択</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="video-area">
          <div id="canvas-container" style="background: url(common/img/canvas2.jpg) no-repeat;	background-size:100%;">
            <canvas id="c" onclick="drawSquare(event)" onmousemove="mouseMove(event)"></canvas>
          </div>
          <div id="debug"></div>
          <div class="btns" id="direction">
            <button type="submit" class="ok">決定</button>
          </div>
        </div>
        <input type="hidden" id="is_rule_number" name="is_rule_number">
      </form>
    </div>
  </div>

@endsection
