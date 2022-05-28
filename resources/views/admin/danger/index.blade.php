@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li>危険エリア侵入検知</li>
        <li>ルール一覧</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">ルール一覧</h2>
        <div class="new-btn"><a href="{{route('admin.danger.create')}}">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
            <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
            <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
          </svg>
          新規登録</a> </div>
      </div>
      <form action="area_search_result.php" method="post" name="form1" id="form1">
        <div class="scroll">
          <table class="table2 text-centre">
            <thead>
              <tr>
                <th>編集</th>
                <th>カメラNo</th>
                <th>現場名</th>
                <th>設置フロア</th>
                <th>設置場所</th>
                <th>アクション</th>
                <th>カラー</th>
															  <th>検知履歴</th>
              </tr>
            </thead>
            <tbody>
           					  <tr>
                <td><button type="button" class="edit" onclick="location.href='{{route('admin.danger.edit')}}'">編集</button></td>
                <td> 12345</td>
                <td>（仮称）ＧＳプロジェクト新築工事</td>
                <td>3階</td>
                <td>トイレ横の資材置き場</td>
                <td>横たわる</td>
															<td><input type="color" id="color1" name="color1" value="#C00000" disabled=""></td>
               <td><button type="button" class="history">履歴表示</button></td>
              </tr>
													  <tr>
                <td><button type="button" class="edit" onclick="location.href='{{route('admin.danger.edit')}}'">編集</button></td>
                <td> 12345</td>
                <td>（仮称）ＧＳプロジェクト新築工事</td>
                <td>3階</td>
                <td>トイレ横の資材置き場</td>
                <td>侵入する</td>
                <td><input type="color" id="color2" name="color2" value="#2CC30E" disabled=""></td>
               <td><button type="button" class="history">履歴表示</button></td>
              </tr>
																						  <tr>
                <td><button type="button" class="edit" onclick="location.href='{{route('admin.danger.edit')}}'">編集</button></td>
                <td> 12345</td>
                <td>（仮称）ＧＳプロジェクト新築工事</td>
                <td>3階</td>
                <td>トイレ横の資材置き場</td>
                <td>寄りかかる</td>
                <td><input type="color" id="color3" name="color3" value="#FFE100" disabled=""></td>
               <td><button type="button" class="history">履歴表示</button></td>
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
