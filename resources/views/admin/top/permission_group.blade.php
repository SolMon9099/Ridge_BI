@extends('admin.layouts.app')

@section('content')

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
            @foreach(config('const.pages') as $group_name => $details)
                @foreach ($details as $index => $detail)
                <tr>
                    <td><p>{{$index == 0 ? $group_name : ''}}</p></td>
                    <td><p>{{$detail['name']}}</p></td>
                    @foreach(config('const.authorities') as $authority_id => $authority)
                    <td class="text-centre">
                        <div class="checkbtn-wrap">
                            @if ($authority_id == 1)
                                <input name="checkbox{{$authority_id}}_{{$detail['id']}}" type="checkbox" id="{{$authority_id}}-{{$detail['id']}}" class="opa" checked disabled>
                            @elseif (isset($authority_groups[$authority_id]) && isset($authority_groups[$authority_id][$detail['id']]) &&  $authority_groups[$authority_id][$detail['id']] == 1)
                                <input name="checkbox{{$authority_id}}_{{$detail['id']}}" type="checkbox" id="{{$authority_id}}-{{$detail['id']}}" checked>
                            @else
                                <input name="checkbox{{$authority_id}}_{{$detail['id']}}" type="checkbox" id="{{$authority_id}}-{{$detail['id']}}" >
                            @endif
                        <label for="{{$authority_id}}-{{$detail['id']}}"></label>
                        </div>
                    </td>
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
