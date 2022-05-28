<div class="no-scroll">
    <table class="table">
    <thead>
        <tr>
        <th>グループ名</th>
        <td>
            <input type="text" name="name" placeholder="グループ名を入力" value="{{ old('name', isset($group->name)?$group->name:'')}}">
            @error('name')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </td>
        </tr>
        <tr>
        <th>送信先アドレス<br><button type="button" class="edit left mt5 create_email">追加</button></th>
        <td>
            <ul class="delete-list" id="email_group">
                <?php $emails_registered = old('emails', isset($group->emails)?explode(",", $group->emails):[]);?>
                @if (isset($emails_registered) && count($emails_registered))
                    @foreach($emails_registered as $key=>$email)
                        <li>
                            <input type="email" name="emails[{{ $key }}]" class="w90" value="{{$email}}">
                            @if (!($loop->first) )
                            <button type="button" class="history2 delete_email">削除</button>
                            @endif
                        </li>
                        @error('emails.' . $key)
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    @endforeach
                @else
                    <li>
                        <input type="email" value="" name="emails[0]">
                    </li>
                    @error('emails.0')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    <li>
                        <input type="email" value="" name="emails[1]" class="w90"><button type="button" class="history2 delete_email">削除</button>
                    </li>
                    @error('emails.1')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                @endif
            </ul>
            @error('emails')
            <p class="error-message">{{ $message }}</p>
            @enderror

        </td>
        </tr>

    </thead>
    </table>
</div>

<script>
  $(document).ready(function() {
    $(".create_email").click(function(e) {
      e.preventDefault();
      let child_email = document.createElement("li");
      child_email.innerHTML = "<input type='email' value='' name='emails[]' class='w90'><button type='button' class='history2 delete_email'>削除</button>";
      document.getElementById('email_group').appendChild(child_email);
    });
    $("#email_group").on("click", ".delete_email", function() {
      $(this).parent().remove();
    });
  });
</script>
