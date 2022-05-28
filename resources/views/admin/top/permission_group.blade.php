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
                @foreach($authorities as $authority)
                <td class="w25"><p class="text-centre">{{$authority->name}}</p></td>
                @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($page_groups as $page_group)
                <tr>
                    <td><p>{{$page_group->group_name}}</p></td>
                    <td><p>{{$page_group->detail_name}}</p></td>

                    <!-- <td class="text-centre"><div class="checkbtn-wrap">
                        <input name="checkbox" type="checkbox" id="{{1-14}}" class="opa" checked="" disabled="">
                        <label for="1-14"></label>
                    </div></td> -->
                    @foreach($authorities as $authority)
                    <td class="text-centre"><div class="checkbtn-wrap">
                        @if ($authority->id == 1)
                        <input name="checkbox{{$authority->id}}_{{$page_group->id}}" type="checkbox" id="{{$authority->id}}-{{$page_group->id}}" class="opa" checked disabled>
                        @elseif ($authority_groups[$authority->id][$page_group->id] == 1)
                        <input name="checkbox{{$authority->id}}_{{$page_group->id}}" type="checkbox" id="{{$authority->id}}-{{$page_group->id}}" checked>
                        @else
                        <input name="checkbox{{$authority->id}}_{{$page_group->id}}" type="checkbox" id="{{$authority->id}}-{{$page_group->id}}" >
                        @endif
                        <label for="{{$authority->id}}-{{$page_group->id}}"></label>
                    </div></td>
                    @endforeach
                </tr>
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
