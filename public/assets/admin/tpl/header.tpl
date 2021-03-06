<?php
define("root" ,"/bi/");
?>
<div class="overlay" id="js__overlay"></div>
<div id="sp-head">
	<h1><img src="common/img/logo-top.svg"></h1>
    <p class="spnav"><a id="btn"><span></span></a></p>
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
                    <span>ログアウト</span>
                </button>
                </li>
                <li>
                    <button type="button" class="cog">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="fill: rgba(0, 98, 222, 1);transform: ;msFilter:;"><path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"></path><path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"></path></svg>
                    </button>
                    <ul class="hidden-box">
                        <li><h2>権限設定</h2>
                            <ul>
                                <li><a href="authority_group.php">権限グループ設定</a></li>
                                <li><a href="account.php">アカウント管理</a></li>
                                <li><a href="notifications.php">通知設定</a></li>
                            </ul>
                        </li>
                        <li><h2>現場設定</h2>
                            <ul>
                                <li><a href="location.php">現場名一覧</a></li>
                            </ul>
                        </li>
                        <li><h2>カメラ設定</h2>
                            <ul>
                                <li><a href="camera.php">カメラ一覧</a></li>
                                <li><a href="camera_mapping.php">カメラマッピング</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
            </div>
        </div>
    </div>
</div>
<header>
    <div id="header">
        <div class="leftside">
            <h1><img src="common/img/logo-top.svg"></h1>
        </div>
    </div>
<ul class="nav" id="dropmenu">

  <li><a href="#">ピット入退場検知</a>
    <ul>
      <li><a href="pit_area.php">ルール一覧</a></li>
      <li><a href="pit_list.php">検知リスト</a></li>
      <li><a href="pit_list2.php">詳細分析</a></li>
    </ul>
  </li>
  <li><a href="#">危険エリア侵入検知</a>
    <ul>
      <li><a href="danger_area.php">ルール一覧</a></li>
      <li><a href="danger_list.php">検知リスト</a></li>
      <li><a href="danger_list2.php">詳細分析</a></li>
    </ul>
  </li>
  <li><a href="#">棚乱れ検知</a>
    <ul>
      <li><a href="shelf_area.php">ルール一覧</a></li>
      <li><a href="shelf_list.php">検知リスト</a></li>
      <li><a href="shelf_list2.php">詳細分析</a></li>
    </ul>
  </li>
  <li><a href="#">検針メーター検知</a>
    <ul>
      <li><a href="meter_area.php">ルール一覧</a></li>
      <li><a href="meter_list.php">検知リスト</a></li>
      <li><a href="meter_list2.php">詳細分析</a></li>
    </ul>
  </li>
  <!--
      <li><a href="#">在席判定</a>
        <ul>
          <li><a href="worker.php">作業員管理</a></li>
          <li><a href="area_search.php">在席エリア判定(アーカイブ)</a></li>
          <li><a href="area_search_real.php">在席エリア判定(リアルタイム)</a></li>
          <li><a href="area_history.php">判定検索履歴</a></li>
        </ul>
      </li>
-->
  <li><a href="#">過去分析</a>
    <ul>
      <li><a href="analyze.php">新規分析依頼</a></li>
      <li><a href="analyze_now_list.php">分析依頼中リスト</a></li>
      <li><a href="analyze_finish_list.php">分析済みリスト</a></li>
    </ul>
  </li>

  <!--nav-->
</ul>
<!-- -->
</header>
