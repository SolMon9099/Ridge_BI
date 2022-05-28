@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li>過去分析</li>
        <li>新規分析依頼</li>
      </ul>
    </div>
    <div id="r-content">
      <div class="title-wrap">
        <h2 class="title">新規分析依頼</h2>
      </div>
      <form action="" method="post" name="form1" id="form1">
        <div class="no-scroll">
          <table class="table">
            <thead>
              <tr>
                <th>対象カメラ</th>
                <td><p class="w25"> <a data-target="rule" class="modal-open setting">選択する</a></p></td>
              </tr>
              <tr>
                <th>分析期間</th>
                <td><ul class="date-list">
                    <li>
                      <input type="date" value="2022-05-11">
                    </li>
                    <li>～</li>
                    <li>
                      <input type="date" value="2022-05-11">
                    </li>
                  </ul></td>
              </tr>
          </thead></table>
        </div>
        <div class="btns">
          <button type="submit" class="ok">分析依頼</button>
        </div>
      </form>
    </div>
  </div>

@endsection
