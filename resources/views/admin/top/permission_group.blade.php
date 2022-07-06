@extends('admin.layouts.app')

@section('content')
<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $headers = isset($login_user->header_menu_ids)?explode(",", $login_user->header_menu_ids):[];

    $display_pages = array();
    $all_pages = config('const.pages');
    if ($super_admin_flag){
        $display_pages = $all_pages;
    } else {
        foreach ($all_pages as $group_name => $sub_pages) {
            if (in_array($group_name, config('const.admin_pages'))){
                $display_pages[$group_name] = $sub_pages;
            } else {
                $id = array_search($group_name, config('const.header_menus'));
                if (in_array($id, $headers)){
                    $display_pages[$group_name] = $sub_pages;
                }
            }
        }
    }

?>
<div id="wrapper">
    <div class="breadcrumb">
      <ul>
        <li>権限設定</li>
        <li>権限グループ設定</li>
      </ul>
    </div>
    <div id="r-content">
    <div class="title-wrap">
      <h2 class="title">権限グループ設定</h2>
    </div>
    @include('admin.layouts.flash-message')
    <form action="{{route('admin.top.permission_store')}}" method="post" name="form1" id="form1">
        @csrf
        <div class="scroll">
        <table class="table2">
            <thead>
                <tr>
                    <td></td>
                    <td></td>
                    @foreach(config('const.authorities') as $authority)
                        <td class="w25"><p class="text-centre">{{$authority}}</p></td>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach($display_pages as $group_name => $details)
                @foreach ($details as $index => $detail)
                <tr>
                    <td><p>{{$index == 0 ? $group_name : ''}}</p></td>
                    <td><p>{{$detail['name']}}</p></td>
                    @foreach(config('const.authorities') as $authority_id => $authority)
                    @if ($authority_id ==config('const.authorities_codes.admin') || !in_array($group_name, config('const.admin_pages')))
                        <td class="text-centre">
                            <div class="checkbtn-wrap">
                                @if ($authority_id == config('const.authorities_codes.admin'))
                                    <input type="checkbox" class="opa" checked disabled>
                                @elseif (isset($authority_groups[$authority_id]) && isset($authority_groups[$authority_id][$detail['id']]) &&  $authority_groups[$authority_id][$detail['id']] == 1)
                                    <input name="checkbox{{$authority_id}}_{{$detail['id']}}" type="checkbox" id="{{$authority_id}}-{{$detail['id']}}" checked>
                                @else
                                    <input name="checkbox{{$authority_id}}_{{$detail['id']}}" type="checkbox" id="{{$authority_id}}-{{$detail['id']}}" >
                                @endif
                                <label class="custom-style" for="{{$authority_id}}-{{$detail['id']}}"></label>
                            </div>
                        </td>
                    @else
                        <td></td>
                    @endif
                    @endforeach
                </tr>
                @endforeach
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="btns">
          <button type="submit" class="ok">更新</button>
        </div>
      </form></div>

  </div>

@endsection
