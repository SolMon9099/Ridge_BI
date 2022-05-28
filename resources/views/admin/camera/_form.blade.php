<div class="no-scroll">
    <table class="table">
        <thead>
            <tr>
                <th>カメラNo</th>
                <td><input type="text" placeholder="カメラNoを入力してください" name="camera_id" value="{{ old('camera_id', isset($camera->camera_id)?$camera->camera_id:'')}}">
                @error('camera_id')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            <tr>
                <th>現場名</th>
                <td>
                    <div class="select-c">
                    <select name="location_id">
                        <option value="">現場名を選択してください</option>
                        @foreach($locations as $key => $location)
                            @if (old('location_id', isset($camera->location_id)?$camera->location_id:'') == $key)
                            <option value="{{$key}}" selected>{{$location}}</option>
                            @else
                            <option value="{{$key}}">{{$location}}</option>
                            @endif
                        @endforeach
                    </select>
                    </div>
                @error('location_id')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            <tr>
                <th>設置フロア</th>
                <td><input type="text" placeholder="設置フロアを入力してください" name="installation_floor" value="{{ old('installation_floor', isset($camera->installation_floor)?$camera->installation_floor:'')}}">
                @error('installation_floor')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            <tr>
                <th>設置場所</th>
                <td><input type="text" placeholder="設置場所を入力してください" name="installation_position" value="{{ old('installation_position', isset($camera->installation_position)?$camera->installation_position:'')}}">
                @error('installation_position')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td><textarea name="remarks">{{ old('remarks', isset($camera->remarks)?$camera->remarks:'')}}</textarea></td>
            </tr>
            <tr>
                <th>稼働状況</th>
                <td>      <ul class="radio-list">
                    @foreach (config('const.camera_status') as $key => $status )
                        <li><input name="is_enabled" type="radio" id="is_enabled_{{ $key }}" value="{{ $key }}" {{ old('is_enabled', isset($camera->is_enabled) ? $camera->is_enabled : config('const.enable_status_code.enable')) == $key ? 'checked' : ''  }}>
                        <label for="is_enabled_{{ $key }}">{{  $status }}</label></li>
                    @endforeach

                </ul></td>
            </tr>
        </thead>
    </table>
</div>
