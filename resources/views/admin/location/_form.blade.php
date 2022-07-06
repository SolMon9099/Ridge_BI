<div class="scroll active">
    <div class="no-scroll">
        <table class="table">
            <thead>
            <tr>
                <th>現場コード</th>
                <td><input type="text" placeholder="現場コードを入力してください" name="code" value="{{ old('code', isset($location->code)?$location->code:'')}}">
                @error('code')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            <tr>
                <th>現場名</th>
                <td><input type="text" placeholder="現場名を入力してください" name="name" value="{{ old('name', isset($location->name)?$location->name:'')}}">
                @error('name')
                <p class="error-message">{{ $message }}</p>
                @enderror
                </td>
            </tr>
            {{-- <tr>
                <th>現場責任者<br>
                <button type="button" class="edit left mt5 create_owner">追加</button></th>
                <td>
                <ul class="delete-list" id="owner_group">
                    <?php $owners_registered = old('owners', isset($location->owner)?explode(",", $location->owner):[]);?>
                    @if (isset($owners_registered) && count($owners_registered)> 0)
                        @foreach($owners_registered as $owner)
                        <li>
                        <select name="owners[]" class="w90">
                            <option value="0">選択する</option>
                            @foreach($owners as $admin)
                                @if ($owner == $admin->id)
                                    <option value="{{$admin->id}}" selected>{{$admin->name}}</option>
                                @else
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (!($loop->first) )
                            <button type="button" class="history2 delete_owner">削除</button>
                        @endif
                        </li>
                        @endforeach
                    @else
                    <li>
                    <select name="owners[]" class="w90">
                        <option value="0">選択する</option>
                        @foreach($owners as $admin)
                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                    </li>
                    <li>
                    <select name="owners[]" class="w90">
                        <option value="0">選択する</option>
                        @foreach($owners as $admin)
                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                    <button type="button" class="history2 delete_owner">削除</button>
                    </li>
                    @endif
                </ul>
                </td>
            </tr> --}}
            <tr>
                <th>現場担当者<br>
                <button type="button" class="edit left mt5 create_manager">追加</button></th>
                <td>
                <ul class="delete-list" id="manager_group">
                    <?php $managers_registered = old('managers', isset($location->manager)?explode(",", $location->manager):[]);?>
                    @if (isset($managers_registered) && count($managers_registered) > 0)
                    @foreach($managers_registered as $manager)
                        <li>
                        <select name="managers[]" class="w90">
                            <option value="0">選択する</option>
                            @foreach($managers as $admin)
                            @if ($manager == $admin->id)
                            <option value="{{$admin->id}}" selected>{{$admin->name}}</option>
                            @else
                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                            @endif
                            @endforeach
                        </select>
                        @if (!($loop->first) )
                        <button type="button" class="history2 delete_manager">削除</button>
                        @endif
                        </li>
                    @endforeach
                    @else
                    <li>
                    <select name="managers[]" class="w90">
                        <option value="0">選択する</option>
                        @foreach($managers as $admin)
                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                    </li>
                    <li>
                    <select name="managers[]" class="w90">
                        <option value="0">選択する</option>
                        @foreach($managers as $admin)
                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                    <button type="button" class="history2 delete_manager">削除</button>
                    </li>
                    @endif
                </ul>
                </td>
            </tr>
            <tr>
                <th>有効設定</th>
                <td>
                <ul class="radio-list">
                    @foreach (config('const.enable_status') as $key => $status )
                    <li>
                        <input name="is_enabled" type="radio" id="is_enabled_{{ $key }}" value="{{ $key }}"
                            {{ old('is_enabled', isset($location->is_enabled) ? $location->is_enabled : config('const.enable_status_code.enable')) == $key ? 'checked' : ''  }}>
                        <label for="is_enabled_{{ $key }}">{{  $status }}</label>
                    </li>
                    @endforeach
                </ul>
                </td>
            </tr>
        </thead></table>
    </div>
</div>

<script>
  $(document).ready(function() {
    $(".create_owner").click(function(e) {
      e.preventDefault();
      let child_owner = document.createElement("li");
      child_owner.innerHTML = '<select name="owners[]" class="w90">' +
            '<option value="0">選択する</option>' +
            @foreach($owners as $admin)
            '<option value="{{$admin->id}}">{{$admin->name}}</option>' +
            @endforeach
        '</select>' +
        '<button type="button" class="history2 delete_owner">削除</button>';
      document.getElementById('owner_group').appendChild(child_owner);
    });
    $("#owner_group").on("click", ".delete_owner", function() {
      $(this).parent().remove();
    });
    $(".create_manager").click(function(e) {
      e.preventDefault();
      let child_manager = document.createElement("li");
      child_manager.innerHTML = '<select name="managers[]" class="w90">' +
            '<option value="0">選択する</option>' +
            @foreach($managers as $admin)
            '<option value="{{$admin->id}}">{{$admin->name}}</option>' +
            @endforeach
        '</select>' +
        '<button type="button" class="history2 delete_manager">削除</button>';
      document.getElementById('manager_group').appendChild(child_manager);
    });
    $("#manager_group").on("click", ".delete_manager", function() {
      $(this).parent().remove();
    });
  });
</script>
