<div class="overlay" id="js__overlay"></div>
<div id="sp-head">
  <h1><img src="{{ asset('assets/admin/img/logo.svg') }}?1111"></h1>
  <p class="spnav"><a id="btn"><span></span></a></p>
</div>
<div id="leftside">
  <div class="">
    <h1><img src="{{ asset('assets/admin/img/logo.svg') }}"></h1>
    <ul class="nav" id="dropmenu">
      <li><a href="#">権限設定</a>
        <ul>
          <li><a href="{{route('admin.top.permission_group')}}">権限グループ設定</a></li>
          <li><a href="{{route('admin.account')}}">アカウント管理</a></li>
          <li><a href="{{route('admin.notification')}}">通知設定</a></li>
        </ul>
      </li>
      <li class="one"><a href="#">現場設定</a>
        <ul>
          <li><a href="{{route('admin.location')}}">現場名一覧</a></li>
        </ul>
      </li>
      <li><a href="#">カメラ設定</a>
        <ul>
          <li><a href="{{route('admin.camera')}}">カメラ一覧</a></li>
          <li><a href="{{route('admin.camera.mapping')}}">カメラマッピング</a></li>
        </ul>
      </li>
      <li><a href="#">危険エリア侵入検知</a>
        <ul>
          <li><a href="{{route('admin.danger')}}">ルール一覧</a></li>
          <li><a href="{{route('admin.danger.list')}}">検知リスト</a></li>
          <li><a href="{{route('admin.danger.list2')}}">詳細分析</a></li>
        </ul>
      </li>
      <li><a href="#">棚乱れ検知</a>
        <ul>
          <li><a href="{{route('admin.shelf')}}">ルール一覧</a></li>
          <li><a href="{{route('admin.shelf.list')}}">検知リスト</a></li>
          <li><a href="{{route('admin.shelf.list2')}}">詳細分析</a></li>
        </ul>
      </li>
      <li><a href="#">検針メーター検知</a>
        <ul>
          <li><a href="{{route('admin.meter')}}">ルール一覧</a></li>
          <li><a href="{{route('admin.meter.list')}}">検知リスト</a></li>
          <li><a href="{{route('admin.meter.list2')}}">詳細分析</a></li>
        </ul>
      </li>
      <li><a href="#">過去分析</a>
        <ul>
          <li><a href="{{route('admin.analyze')}}">新規分析依頼</a></li>
          <li><a href="{{route('admin.analyze.now_list')}}">分析依頼中リスト</a></li>
          <li><a href="{{route('admin.analyze.finish_list')}}">分析済みリスト</a></li>
        </ul>
      </li>

      <!--nav-->
    </ul>
  </div>
</div>
<div id="rightside">
<div id="r-head">

  <div id="r-head-right">
    <ul>
      <li>
        <button type="button" class="logout">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(208,211,219, 1);transform: ;msFilter:;">
          <path d="M16 13v-2H7V8l-5 4 5 4v-3z"></path>
          <path d="M20 3h-9c-1.103 0-2 .897-2 2v4h2V5h9v14h-9v-4H9v4c0 1.103.897 2 2 2h9c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2z"></path>
        </svg>
        <span  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</span></button>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </li>
    </ul>
  </div>
</div>
<!-- -->
