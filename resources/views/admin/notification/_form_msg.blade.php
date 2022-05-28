
<div class="no-scroll">
    <table class="table">
        <thead>
            <tr>
            <th>メッセージ名</th>
            <td><input type="text" value="{{ old('title', isset($msg->title)?$msg->title:'')}}" name="title" placeholder="メッセージ名を入力">
                @error('title')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </td>
            </tr>
            <tr>
            <th>メッセージ</th>
            <td>
                <textarea placeholder="メッセージを入力" name="content">{{ old('content', isset($msg->content)?$msg->content:'')}}</textarea>
                @error('content')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </td>
            </tr>
        </thead>
    </table>
</div>