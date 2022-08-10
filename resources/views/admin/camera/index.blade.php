<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
?>
@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.camera')}}">カメラ設定</a></li>
            <li>カメラ一覧</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">カメラ一覧</h2>
            @if (!$super_admin_flag)
                <div class="new-btn">
                    <a href="{{route('admin.camera.create')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255,255,255, 1);transform: ;msFilter:;">
                        <path d="M3 16c0 1.103.897 2 2 2h3.586L12 21.414 15.414 18H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12zM5 4h14v12h-4.414L12 18.586 9.414 16H5V4z"></path>
                        <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                    </svg>
                    新規登録</a>
                </div>
            @endif
        </div>
        <form action="{{route('admin.camera')}}" method="get" name="form1" id="form1">
        @csrf
        <div class="title-wrap ver2 stick">
            <div class="sp-ma">
                <div class="sort">
                    <ul class="date-list">
                        <li><h4>設置エリア</h4></li>
                        <li>
                            <div class="select-c">
                                <select name="location">
                                <option>選択する</option>
                                @foreach($locations as $key => $loc)
                                    @if (isset($input) && $input->has('location') && $input->location == $key)
                                    <option value="{{$key}}" selected>{{$loc}}</option>
                                    @else
                                    <option value="{{$key}}">{{$loc}}</option>
                                    @endif
                                @endforeach
                                </select>
                            </div>
                        </li>
                    </ul>
                    <ul class="date-list">
                        <li><h4>設置フロア</h4></li>
                        <li>
                            <div>
                                <input type="text" name="floor_number" value="{{ old('floor_number', (isset($input) && $input->has('floor_number'))?$input->floor_number:'')}}"/>
                            </div>
                        </li>
                    </ul>
                    <ul class="date-list">
                        <li><h4>稼働状況 </h4></li>
                        <li>
                            <ul class="radio-list">
                                @foreach (config('const.camera_status') as $key => $status )
                                    <li>
                                        <input name="is_enabled" type="radio" id="is_enabled_{{ $key }}" value="{{ $key }}"
                                        {{ old('is_enabled', (isset($input) && $input->has('is_enabled') && $input->is_enabled != '') ? $input->is_enabled : config('const.enable_status_code.enable')) == $key ? 'checked' : ''  }}>
                                        <label for="is_enabled_{{ $key }}">{{  $status }}</label>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                    <button type="submit" class="apply">絞り込む</button>
                </div>
            </div>
        </div>
        </form>
        @include('admin.layouts.flash-message')
        {{ $cameras->appends([
            'is_enabled' => isset($input) && $input->has('is_enabled') ? $input->is_enabled : '',
            'location'=>isset($input) && $input->has('location') && $input->location > 0 ? $input->location : '',
            'floor_number'=>isset($input) && $input->has('floor_number') ? $input->floor_number : ''
            ])->links('vendor.pagination.admin-pagination') }}
        <ul class="camera-list">
        @foreach($cameras as $camera)
            <li>
                <div class="pic"><img src="{{ $camera->img }}"></div>
                <div class="text">
                <button style="background:transparent;border:none;" class="edit2" onclick="location.href='{{route('admin.camera.edit', ['camera' => $camera->id])}}'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="fill: rgba(46, 191, 67, 1);transform: ;msFilter:;">
                    <path d="M16 2H8C4.691 2 2 4.691 2 8v13a1 1 0 0 0 1 1h13c3.309 0 6-2.691 6-6V8c0-3.309-2.691-6-6-6zM8.999 17H7v-1.999l5.53-5.522 1.999 1.999L8.999 17zm6.473-6.465-1.999-1.999 1.524-1.523 1.999 1.999-1.524 1.523z"></path>
                    </svg>
                </button>
                <table class="table">
                    <tr>
                        <th>カメラNo.</th>
                        <td>{{$camera->camera_id}}</td>
                    </tr>
                    <tr>
                        <th>設置エリア</th>
                        <td>{{isset($locations[$camera->location_id])?$locations[$camera->location_id]:''}}</td>
                    </tr>
                    <tr>
                        <th>設置フロア</th>
                        <td>{{$camera->floor_number}}</td>
                    </tr>
                    <tr>
                        <th>設置場所</th>
                        <td>{{$camera->installation_position}}</td>
                    </tr>
                </table>
                </div>
            </li>
        @endforeach
        </ul>
        {{ $cameras->appends([
            'is_enabled' => isset($input) && $input->has('is_enabled') ? $input->is_enabled : '',
            'location'=>isset($input) && $input->has('location') && $input->location > 0 ? $input->location : '',
            'floor_number'=>isset($input) && $input->has('floor_number') ? $input->floor_number : ''
            ])->links('vendor.pagination.admin-pagination') }}
    </div>
</div>

@endsection
