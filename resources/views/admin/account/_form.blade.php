<div class="no-scroll">
    <table class="table">
        <thead>
            <tr>
                <th>権限</th>
                <td>
                    <select name="authority_id">
                        @foreach(config('const.authorities') as $authority_id => $authority)
                        @if (old('authority_id', isset($admin->authority_id)?$admin->authority_id:1) == $authority_id)
                            <option value="{{$authority_id}}" selected>{{$authority}}</option>
                        @else
                            <option value="{{$authority_id}}">{{$authority}}</option>
                        @endif
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>部門</th>
                <td><input type="text" name="department" value="{{ old('department', isset($admin->department)?$admin->department:'')}}"></td>
            </tr>
            <tr>
                <th>アカウント名</th>
                <td><input type="text" name="name" value="{{ old('name', isset($admin->name)?$admin->name:'')}}">
                    @error('name')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><input type="email" name="email" value="{{ old('email', isset($admin->email)?$admin->email:'')}}">
                    @error('email')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            <tr>
            <th>パスワード</th>
                <td>
                    <input type="password" name="password" id="password" placeholder="英数8文字以上（記号可）" pattern="^[a-zA-Z0-9!-/:-@¥[-`{-~]*$"/>
                    @error('password')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            <tr>
                <th>パスワード確認</th>
                <td>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="英数8文字以上（記号可）" pattern="^[a-zA-Z0-9!-/:-@¥[-`{-~]*$">
                </td>
            </tr>
            <!--
            <tr>
            <th>人物ID</th>
                <td><input type="text" value="1234"></td>
            </tr>
            <tr>
            <th>顔認証用動画</th>
            <td>
                <div class="js-upload-filename no-inline"></div>
                <input type="file" name="file" class="js-upload-file" id="file" accept="video/*"><label for="file" class="image">変更する</label>
            </td>
            </tr>
            -->
            <tr>
                <th>有効設定</th>
                <td>
                    <ul class="radio-list">
                    @foreach (config('const.enable_status') as $key => $status )
                        <li><input name="is_enabled" type="radio" id="is_enabled_{{ $key }}" value="{{ $key }}" {{ old('is_enabled', isset($admin->is_enabled) ? $admin->is_enabled : config('const.enable_status_code.enable')) == $key ? 'checked' : ''  }}>
                        <label for="is_enabled_{{ $key }}">{{  $status }}</label></li>
                    @endforeach
                    </ul>
                </td>
            </tr>
        </thead>
    </table>
</div>
