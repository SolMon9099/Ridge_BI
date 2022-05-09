<?php
define("root" ,"/bi/");
?>
<div class="overlay" id="js__overlay"></div>
<div id="sp-head">
  <h1><img src="common/img/logo.svg"></h1>
  <p class="spnav"><a id="btn"><span></span></a></p>
</div>
<div id="leftside">
  <div class="">
    <h1><img src="common/img/logo.svg"></h1>
    <ul class="nav" id="dropmenu">
      <li><a href="#">権限設定</a>
        <ul>
          <li><a href="authority_group.php">権限グループ設定</a></li>
          <li><a href="account.php">アカウント管理</a></li>
        </ul>
      </li>
					<li class="one"><a href="location.php">現場マスタ</a></li>
      <li><a href="#">在席判定</a>
        <ul>
									<li><a href="worker.php">作業員管理</a></li>
          <li><a href="camera.php">カメラ設定</a></li>
          <li><a href="area_search.php">在席エリア判定(アーカイブ)</a></li>
									 <li><a href="area_search_real.php">在席エリア判定(リアルタイム)</a></li>
          <li><a href="area_history.php">判定検索履歴</a></li>
        </ul>
      </li>
      <li><a href="#">危険エリア侵入判定</a>
        <ul>
          <li><a href="notification.php">通知設定</a></li>
          <li><a href="danger_camera.php">カメラ設定</a></li>
          <li><a href="danger_area.php">危険エリア侵入判定</a></li>
									  <li><a href="danger_list.php">危険エリア検知リスト(アーカイブ)</a></li>
									<li><a href="danger_list_real.php">危険エリア検知リスト(リアルタイム)</a></li>
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
        <span>ログアウト</span></button>
      </li>
    </ul>
  </div>
</div>
<!-- -->