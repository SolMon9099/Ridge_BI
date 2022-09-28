@extends('admin.layouts.app')

@section('content')

    <div id="wrapper">
        <div class="breadcrumb">
            <ul>
                <li><a href="{{route('admin.pit')}}">ピット入退場検知</a></li>
                <li><a href="{{route('admin.pit')}}">ルール一覧・編集</a></li>
                <li>ルール新規作成</li>
            </ul>
        </div>
        <div id="r-content">
            <div class="title-wrap">
                <h2 class="title">ルール新規作成</h2>
            </div>
            <div class="notice-area">検知対象のエリアを設定するカメラを選択してください。<br/>※選択できないカメラはグレーアウトされています。</div>
            <div class="flow"><ul><li class="active"><span>Step.1</span>カメラを選択</li><li><span>Step.2</span>エリア選択・検知設定</li></ul></div>
            <form action="{{route('admin.pit.create_rule')}}" method="get" name="form1" id="form1">
                @csrf
                {{ $cameras->appends([])->links('vendor.pagination.admin-pagination') }}
                <div class="scroll">
                    <table class="table2 text-centre">
                        <thead>
                        <tr>
                            <th>選択</th>
                            <th>カメラNo</th>
                            <th>設置エリア</th>
                            <th>設置フロア</th>
                            <th>設置場所</th>
                            <th>ルール登録数</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cameras as $camera)
                            <tr class="{{count($camera->rules) > 0 ? 'disabled-tr' : ''}}">
                                <td>
                                    @if(count($camera->rules) == 0)
                                    <div class="radio">
                                        <input onclick="enableSubmitButton()" id="radio-{{$camera->id}}" name="selected_camera" type="radio" value="{{$camera->id}}">
                                        <label for="radio-{{$camera->id}}" class="radio-label"></label>
                                    </div>
                                    @endif
                                </td>
                                <td>{{$camera->camera_id}}</td>
                                <td>{{isset($locations[$camera->location_id])?$locations[$camera->location_id]:''}}</td>
                                <td>{{$camera->floor_number}}</td>
                                <td>{{$camera->installation_position}}</td>
                                <td>{{count($camera->rules)}}/1</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @error('selected_camera')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                {{ $cameras->appends([])->links('vendor.pagination.admin-pagination') }}

                <div class="btns" style="display: none">
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
        .disabled-tr{
            background: lightgray!important;
        }
        .disabled-tr > td{
            background: lightgray!important;
        }
    </style>
    <script>
        function enableSubmitButton(){
            $('.btns').show();
        }
    </script>

@endsection
