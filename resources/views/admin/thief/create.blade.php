@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
         <li>大量盗難探知</li>
        <li>ルール一覧</li>
							<li>ルール新規作成</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">ルール新規作成</h2>
      </div>
					<div class="flow"><ul><li class="active"><span>Step.1</span>カメラを選択</li><li><span>Step.2</span>エリアとしきい値を設定</li></ul></div>
      <form action="{{route('admin.thief.create_rule')}}" method="post" name="form1" id="form1">
        <div class="scroll">
          <table class="table2 text-centre">
            <thead>
              <tr>
                <th>選択</th>
                <th>カメラNo</th>
                <th>現場名</th>
                <th>設置フロア</th>
                <th>設置場所</th>
                <th>備考</th>
                <th>稼働状況</th>
              </tr>
            </thead>
            <tbody>
              <tr>
             <td><div class="radio">
                    <input id="radio-1" name="radio" type="radio">
                    <label for="radio-1" class="radio-label"></label>
                  </div></td>
                <td> 12345</td>
                <td>（仮称）ＧＳプロジェクト新築工事</td>
                <td>3階</td>
                <td>トイレ横の資材置き場</td>
															<td></td>
               <td>稼働中</td>
              </tr>
													  <tr>
               <td><div class="radio">
                    <input id="radio-2" name="radio" type="radio">
                    <label for="radio-2" class="radio-label"></label>
                  </div></td>
                <td> 12345</td>
                <td>（仮称）ＧＳプロジェクト新築工事</td>
                <td>3階</td>
                <td>トイレ横の資材置き場</td>
															<td></td>
               <td>稼働中</td>
              </tr>
            </tbody>
          </table>
        </div>
        <!--
        <div class="tour-content mt25">
          <div class="float-l pager"><a href="">＜</a><span class="current">1</span><a href="">2</a><a href="">3</a><a href="">4</a><a href="">5</a><a href="">＞</a></div>
        </div>
-->
														<div class="btns">
        <button type="submit" class="ok">決定</button>
							</div>
      </form>
    </div>
  </div>

@endsection
