@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.thief')}}">大量盗難検知</a></li>
            <li><a href="{{route('admin.thief')}}">ルール一覧・編集</a></li>
            <li>ルール新規作成</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">ルール新規作成</h2>
        </div>
		<div class="flow"><ul><li class="active"><span>Step.1</span>カメラを選択</li><li><span>Step.2</span>エリア選択・検知設定</li></ul></div>
        <form action="{{route('admin.thief.create_rule')}}" method="post" name="form1" id="form1">
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
                @if($from_add_button == true)
                    <tr class="{{count($camera->rules) == config('const.thief_max_rect_numbers')? 'disabled-tr' : ''}}">
                @else
                    <tr class="{{count($camera->rules) > 0 ? 'disabled-tr' : ''}}">
                @endif
                    <td>
                        @if($from_add_button == true)
                            @if(count($camera->rules) < config('const.thief_max_rect_numbers'))
                            <div class="radio">
                                <input onclick="enableSubmitButton({{$camera->id}})" id="radio-{{$camera->id}}" name="selected_camera" type="radio" value="{{$camera->id}}">
                                <label for="radio-{{$camera->id}}" class="radio-label"></label>
                            </div>
                            @endif
                        @else
                            @if(count($camera->rules) == 0)
                            <div class="radio">
                                <input onclick="enableSubmitButton({{$camera->id}})" id="radio-{{$camera->id}}" name="selected_camera" type="radio" value="{{$camera->id}}">
                                <label for="radio-{{$camera->id}}" class="radio-label"></label>
                            </div>
                            @else
                                @if(count($camera->rules) < config('const.thief_max_rect_numbers'))
                                <div class="radio">
                                    <button type="button" class="edit" onclick="location.href='{{route('admin.thief.edit', ['thief' => $camera->rules[0]->id])}}'">追加登録</button>
                                    <input class='text-camera-id' style="display: none" id="radio-{{$camera->id}}" name="selected_camera" value="">
                                </div>
                                @endif
                            @endif
                        @endif
                    </td>
                    <td>{{$camera->serial_no}}</td>
                    <td>{{isset($locations[$camera->location_id])?$locations[$camera->location_id]:''}}</td>
                    <td>{{$camera->floor_number}}</td>
                    <td>{{$camera->installation_position}}</td>
                    <td>{{count($camera->rules)}}/{{config('const.shelf_max_rect_numbers')}}</td>
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
<script>
    function enableSubmitButton(camera_id){
        $('.btns').show();
        $('.text-camera-id').val(camera_id);
    }
    function createRule(camera_id){
        $('input[name="selected_camera"]').val(camera_id);
        $('#form1').submit();
    }
</script>
@endsection
