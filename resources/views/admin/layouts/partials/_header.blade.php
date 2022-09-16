<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $admin_flag = ($login_user->authority_id == config('const.authorities_codes.admin'));
    $general_user_flag = !($super_admin_flag | $admin_flag);
    $headers = isset($login_user->header_menu_ids)?explode(",", $login_user->header_menu_ids):[];
    $manager_allowed_pages = $login_user->manager_allowed_pages;
    $suffix = '';
    if ($super_admin_flag) $suffix = '（スーパー管理者）';
    if ($admin_flag) $suffix = '（管理者）';

?>
<div class="overlay" id="js__overlay"></div>
<div id="sp-head">
    <a href="{{route('admin.top')}}">
        <h1><img src="{{ asset('assets/admin/img/logo-top.svg') }}?1111"></h1>
    </a>
    <p class="spnav"><a id="btn"><span></span></a></p>

    <div id="rightside">
        <div id="r-head">
            <div id="r-head-right">
                <ul>
                    <li>
                        <div class="login-user-name">{{$login_user->name.$suffix}}</div>
                    </li>
                    <li>
                        <button type="button" class="logout">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(208,211,219, 1);transform: ;msFilter:;">
                                <path d="M16 13v-2H7V8l-5 4 5 4v-3z"></path>
                                <path d="M20 3h-9c-1.103 0-2 .897-2 2v4h2V5h9v14h-9v-4H9v4c0 1.103.897 2 2 2h9c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2z"></path>
                            </svg>
                            <span  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</span>
                        </button>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                    @if($super_admin_flag || $admin_flag)
                    <li>
                        <button type="button" class="cog">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="fill: rgba(0, 98, 222, 1);transform: ;msFilter:;"><path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"></path><path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"></path></svg>
                        </button>
                        <ul class="hidden-box">
                            <li><h2>権限設定</h2>
                                <ul>
                                    <li><a href="{{route('admin.top.permission_group')}}">権限グループ設定</a></li>
                                    <li><a href="{{route('admin.account')}}">アカウント管理</a></li>
                                    <li><a href="{{route('admin.notification')}}">通知設定</a></li>
                                </ul>
                            </li>
                            <li><h2>現場設定</h2>
                                <ul>
                                    <li><a href="{{route('admin.location')}}">設置エリア一覧</a></li>
                                </ul>
                            </li>
                            <li><h2>カメラ設定</h2>
                                <ul>
                                    <li><a href="{{route('admin.camera')}}">カメラ一覧</a></li>
                                    <li><a href="{{route('admin.camera.mapping')}}">カメラマッピング</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
<header>
    <div id="header">
        <div class="leftside">
            <a href="{{route('admin.top')}}">
                <h1>
                    <img src="{{ asset('assets/admin/img/logo-top.svg') }}">
                </h1>
            </a>
        </div>
    </div>
    <ul class="nav" id="dropmenu">
        <li class="top-menu">
            <a href="#">ダッシュボード</a>
            <ul>
                <li><a href="{{route('admin.top')}}">ダッシュボード</a></li>
            </ul>
        </li>
        @foreach (config('const.header_menus') as $code => $header_name)
        @if ($super_admin_flag || in_array($code, $headers))
            <li class="{{config('const.header_menu_classes')[$code]}}"><a href="#">{{$header_name}}</a>
            <ul>
            @foreach (config('const.pages')[$header_name] as $item)
                <?php $page_id = $item['id']; $url = config('const.page_route_names')[$page_id];?>
                @if ($super_admin_flag)
                    @if (!in_array($url, config('const.super_admin_not_allowed_pages')))
                        <li><a href="{{route($url)}}">{{$item['name']}}</a></li>
                    @endif
                @else
                    @if (!$general_user_flag || in_array($url, $manager_allowed_pages))
                        <li><a href="{{route($url)}}">{{$item['name']}}</a></li>
                    @endif
                @endif
            @endforeach
            </ul>
        @endif
        @endforeach
    </ul>
</header>
<style>
    .login-user-name{
        color: #999;
        padding-top: 7px;
        padding-left:10px;
        padding-bottom: 7px;
        background: white;
        margin-right: 10px;
        border-radius: 40px;
    }
    @media only screen and (max-width:768px) {
        .login-user-name{
            display: none;
        }
    }
</style>
