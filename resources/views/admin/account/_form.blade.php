<?php
    $login_user = Auth::guard('admin')->user();
    $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
    $edit_user_authority = old('authority_id', isset($admin->authority_id)?$admin->authority_id:1);
    $is_main_admin = 0;
    if (isset($admin->is_main_admin)){
        $is_main_admin = $admin->is_main_admin;
    }
?>
<div class="no-scroll">
    <table class="table">
        <thead>
            <tr>
                <th>権限</th>
                <td>
                    @if ($super_admin_flag)
                        @if ($edit_user_authority == config('const.super_admin_code'))
                            {{config('const.super_admin')[config('const.super_admin_code')]}}
                        @else
                            <select name="authority_id" readonly>
                                <option value="{{config('const.authorities_codes.admin')}}" selected>{{config('const.authorities')[config('const.authorities_codes.admin')]}}</option>
                            </select>
                        @endif
                    @else
                        @if($is_main_admin)
                        <select name="authority_id" readonly>
                            <option value="{{config('const.authorities_codes.admin')}}" selected>{{config('const.authorities')[config('const.authorities_codes.admin')]}}</option>
                        </select>
                        @else
                        <select name="authority_id">
                            @foreach(config('const.authorities') as $authority_id => $authority)
                            @if (old('authority_id', isset($admin->authority_id)?$admin->authority_id:1) == $authority_id)
                                <option value="{{$authority_id}}" selected>{{$authority}}</option>
                            @else
                                <option value="{{$authority_id}}">{{$authority}}</option>
                            @endif
                            @endforeach
                        </select>
                        @endif
                    @endif

                </td>
            </tr>
            <tr>
                <th>部門</th>
                <td><input type="text" name="department" value="{{ old('department', isset($admin->department)?$admin->department:'')}}"></td>
            </tr>
            <?php
                $headers = [];
                if (isset($admin)){
                    $headers = old('headers', isset($admin->header_menu_ids)?explode(",", $admin->header_menu_ids):[]);
                } else {
                    $headers = isset($login_user->header_menu_ids)?explode(",", $login_user->header_menu_ids):[];
                }
            ?>
            <tr>
                <th>
                    ヘッダメニュー<br/>
                    <button type="button" class="edit left mt5 create_header">追加</button>
                </th>
                <td>
                    <ul class="delete-list" id="header_group">
                        @if (isset($headers) && count($headers)> 0)
                            @foreach($headers as $header_id)
                                <li>
                                <select name="headers[]" class="w90">
                                    <option value="0">選択する</option>
                                    @foreach(config('const.header_menus') as $h_id => $header_name)
                                    @if ($header_id == $h_id)
                                    <option value="{{$h_id}}" selected>{{$header_name}}</option>
                                    @else
                                    <option value="{{$h_id}}">{{$header_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @if (!($loop->first) )
                                <button type="button" class="history2 delete_header">削除</button>
                                @endif
                                </li>
                            @endforeach
                        @else
                            <li>
                            <select name="headers[]" class="w90">
                                <option value="0">選択する</option>
                                @foreach(config('const.header_menus') as $h_id => $header_name)
                                <option value="{{$h_id}}">{{$header_name}}</option>
                                @endforeach
                            </select>
                            </li>
                            <li>
                            <select name="headers[]" class="w90">
                                <option value="0">選択する</option>
                                @foreach(config('const.header_menus') as $h_id => $header_name)
                                <option value="{{$h_id}}">{{$header_name}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="history2 delete_header">削除</button>
                            </li>
                        @endif
                    </ul>
                </td>
            </tr>
            @if ($edit_user_authority != config('const.super_admin_code') && $super_admin_flag)
                <input name = 'contract_no' type="hidden" value="{{ old('contract_no', isset($admin->contract_no)?$admin->contract_no:'')}}"/>
                {{-- <tr>
                    <th>契約ID</th>
                    <td>
                        <input style="background:white" name = 'contract_no' type="number" value="{{ old('contract_no', isset($admin->contract_no)?$admin->contract_no:'')}}"/>
                        @error('contract_no')
                        <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>セーフィーID</th>
                    <td>
                        <input type='text' name = 'safie_user_name' value="{{ old('safie_user_name', isset($admin->safie_user_name)?$admin->safie_user_name:'')}}"/>
                        @error('safie_user_name')
                        <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>セーフィーパス</th>
                    <td>
                        <input type='text' name = 'safie_password' value="{{ old('safie_password', isset($admin->safie_password)?$admin->safie_password:'')}}"/>
                        @error('safie_password')
                        <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>client_id</th>
                    <td>
                        <input type='text' name = 'safie_client_id' value="{{ old('safie_client_id', isset($admin->safie_client_id)?$admin->safie_client_id:'')}}"/>
                        @error('safie_client_id')
                        <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>client_secret</th>
                    <td>
                        <input type='text' name = 'safie_client_secret' value="{{ old('safie_client_secret', isset($admin->safie_client_secret)?$admin->safie_client_secret:'')}}"/>
                        @error('safie_client_secret')
                        <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr> --}}
                {{-- <tr>
                    <th>
                        ヘッダメニュー<br/>
                        <button type="button" class="edit left mt5 create_header">追加</button>
                    </th>
                    <td>
                        <ul class="delete-list" id="header_group">
                            @if (isset($headers) && count($headers)> 0)
                                @foreach($headers as $header_id)
                                    <li>
                                    <select name="headers[]" class="w90">
                                        <option value="0">選択する</option>
                                        @foreach(config('const.header_menus') as $h_id => $header_name)
                                        @if ($header_id == $h_id)
                                        <option value="{{$h_id}}" selected>{{$header_name}}</option>
                                        @else
                                        <option value="{{$h_id}}">{{$header_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @if (!($loop->first) )
                                    <button type="button" class="history2 delete_header">削除</button>
                                    @endif
                                    </li>
                                @endforeach
                            @else
                                <li>
                                <select name="headers[]" class="w90">
                                    <option value="0">選択する</option>
                                    @foreach(config('const.header_menus') as $h_id => $header_name)
                                    <option value="{{$h_id}}">{{$header_name}}</option>
                                    @endforeach
                                </select>
                                </li>
                                <li>
                                <select name="headers[]" class="w90">
                                    <option value="0">選択する</option>
                                    @foreach(config('const.header_menus') as $h_id => $header_name)
                                    <option value="{{$h_id}}">{{$header_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="history2 delete_header">削除</button>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr> --}}
            @elseif (!$super_admin_flag)
                <input name = 'contract_no' type="hidden" readonly
                            value="{{ isset($admin->contract_no)?$admin->contract_no:(isset($login_user->contract_no) ? $login_user->contract_no : '')}}"/>
                {{-- <tr>
                    <th>契約ID</th>
                    <td>
                        <input style="background:white" name = 'contract_no' type="number" readonly
                            value="{{ isset($admin->contract_no)?$admin->contract_no:(isset($login_user->contract_no) ? $login_user->contract_no : '')}}"/>
                    </td>
                </tr> --}}
                {{-- <tr>
                    <th>ヘッダメニュー</th>
                    <td>
                        <ul class="delete-list">
                            @foreach ($headers as $header_id)
                            <select name="headers[]" readonly>
                                <option value="{{$header_id}}" selected>{{isset(config('const.header_menus')[$header_id])?config('const.header_menus')[$header_id]:''}}</option>
                            </select>
                            @endforeach
                        </ul>
                    </td>
                </tr> --}}
                {{-- <tr> --}}
                    {{-- <th>担当現場</th> --}}
                    {{-- <td><a data-target="rule" class="modal-open setting white">クリックして現場を選択してください</a></td> --}}
                {{-- </tr> --}}
            @endif
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
                <td>
                    @if($is_main_admin)
                        <input readonly type="email" name="email" value="{{ old('email', isset($admin->email)?$admin->email:'')}}">
                    @else
                        <input type="email" name="email" value="{{ old('email', isset($admin->email)?$admin->email:'')}}">
                    @endif

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
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="英数8文字以上（記号可）" pattern="^[a-zA-Z0-9!-/:-@¥[-`{-~]*$"/>
                </td>
            </tr>
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
<script>
    $(document).ready(function() {
      $(".create_header").click(function(e) {
        e.preventDefault();
        let child_header = document.createElement("li");
        child_header.innerHTML = '<select name="headers[]" class="w90">' +
              '<option value="0">選択する</option>' +
              @foreach(config('const.header_menus') as $h_id => $header_name)
              '<option value="{{$h_id}}">{{$header_name}}</option>' +
              @endforeach
          '</select>' +
          '<button type="button" class="history2 delete_header">削除</button>';
        document.getElementById('header_group').appendChild(child_header);
      });
      $("#header_group").on("click", ".delete_header", function() {
        $(this).parent().remove();
      });
    });
  </script>
