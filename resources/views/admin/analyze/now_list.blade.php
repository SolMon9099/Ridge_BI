@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li>過去分析</li>
        <li>新規分析依頼中リスト</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">新規分析依頼中リスト</h2>
      </div>
      <form action="" method="post" name="form1" id="form1">
        <div class="scroll active">
          <table class="table2 text-centre top50">
            <thead>
              <tr>
                <th>依頼日時</th>
                <th>カメラNo</th>
                <th>設置エリア</th>
                <th>設置フロア</th>
                <th>設置場所</th>
                <th>期間</th>
                <th>キャンセル</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2022/2/10 11:00</td>
                <td> 12345</td>
                <td>（仮称）ＧＳプロジェクト新築工事</td>
                <td>3階</td>
                <td>トイレ横の資材置き場</td>
                <td>2022/05/11～2022/05/18</td>
                <td><button type="button" class="history">キャンセル</button></td>
              </tr>
              <tr>
                <td>2022/2/10 10:00</td>
                <td> 12345</td>
                <td>（仮称）ＧＳプロジェクト新築工事</td>
                <td>3階</td>
                <td>トイレ横の資材置き場</td>
                <td>2022/05/11～2022/05/18</td>
                <td><button type="button" class="history">キャンセル</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </form>
    </div>
  </div>

@endsection
