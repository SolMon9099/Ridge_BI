@extends('admin.layouts.app')

@section('content')

    <div id="wrapper">
        <div class="breadcrumb">
            <ul>
                <li><a href="{{route('admin.danger')}}">危険エリア侵入検知</a></li>
                <li><a href="{{route('admin.danger')}}">ルール一覧</a></li>
                <li>ルール新規作成</li>
            </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">ルール新規作成</h2>
            </div>
            <div class="notice-area">検知対象のエリアを設定するカメラを選択してください。</div>
            <div class="flow"><ul><li class="active"><span>Step.1</span>カメラを選択</li><li><span>Step.2</span>エリア選択・検知設定</li></ul></div>
            <form action="{{route('admin.danger.create_rule')}}" method="get" name="form1" id="form1">
                @csrf
                {{ $cameras->appends([])->links('vendor.pagination.admin-pagination') }}
                <div class="scroll">
                    <table class="table2 text-centre">
                        <thead>
                        <tr>
                            <th>選択</th>
                            <th>カメラNo</th>
                            <th>現場名</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
                            <th>備考</th>
                            <th>稼働状況</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cameras as $camera)
                            <tr>
                                <td>
                                    <div class="radio">
                                        <input id="radio-{{$camera->id}}" name="selected_camera" type="radio" value="{{$camera->id}}">
                                        <label for="radio-{{$camera->id}}" class="radio-label"></label>
                                    </div>
                                </td>
                                <td>{{$camera->camera_id}}</td>
                                <td>{{isset($locations[$camera->location_id])?$locations[$camera->location_id]:''}}</td>
                                <td>{{$camera->floor_number}}</td>
                                <td>{{$camera->installation_position}}</td>
                                <td>{{$camera->remarks}}</td>
                                <td>{{config('const.camera_status')[$camera->is_enabled]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @error('selected_camera')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                {{ $cameras->appends([])->links('vendor.pagination.admin-pagination') }}

                <div class="btns">
                    <button type="submit" class="ok">決定</button>
                </div>
            </form>
        </div>
    </div>
    <style>
        .notice-area{
            color: #999;
            margin-bottom: 8px;
        }
    </style>
@endsection
