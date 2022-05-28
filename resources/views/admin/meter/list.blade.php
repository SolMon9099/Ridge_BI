@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li>検針メーター検知</li>
        <li>検知リスト</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">検知リスト</h2>
      </div>
      <form action="meter_list.php" method="post" name="form1" id="form1">
        <div class="title-wrap ver2 stick">
          <div class="sp-ma">
            <div class="sort">
              <ul class="date-list">
                <li>
                  <h4>検出期間</h4>
                </li>
                <li>
                  <input type="date" value="2022-05-18">
                </li>
                <li>～</li>
                <li>
                  <input type="date" value="2022-05-18">
                </li>
              </ul>
													<!--
              <ul class="date-list">
                <li>
                  <h4>滞在時間</h4>
                </li>
                <li>
                  <input type="tel" value="">
                </li>
                <li>秒以上</li>
              </ul>
-->
              <ul class="date-list">
                <li>
                  <h4>ルール</h4>
                </li>
                <li><a data-target="rule" class="modal-open setting">選択する</a></li>
              </ul>
              <button type="button" class="apply">絞り込む</button>
            </div>
          </div>
        </div>


            <div class="scroll active">
              <table class="table2 text-centre top50">
                <thead>
                  <tr>
                    <th>検知日時</th>
                    <th>カメラNo</th>
                    <th>現場名</th>
                    <th>設置フロア</th>
                    <th>設置場所</th>
                    <th>アクション</th>
                    <th>動画再生</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>2022/2/10 11:00</td>
                    <td> 12345</td>
                    <td>（仮称）ＧＳプロジェクト新築工事</td>
                    <td>3階</td>
                    <td>トイレ横の資材置き場</td>
                    <td>侵入する</td>
                    <td><a data-target="movie0000" class="modal-open play">再生</a></td>
                  </tr>
                  <tr>
                    <td>2022/2/10 10:00</td>
                    <td> 12345</td>
                    <td>（仮称）ＧＳプロジェクト新築工事</td>
                    <td>3階</td>
                    <td>トイレ横の資材置き場</td>
                    <td>手を挙げる</td>
                    <td><a data-target="movie0001" class="modal-open play">再生</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
        
          

        <!--
        <div class="tour-content mt25">
          <div class="float-l pager"><a href="">＜</a><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a><a href="">＞</a></div>
        </div>
-->
        
      </form>
    </div>
  </div>

@endsection
