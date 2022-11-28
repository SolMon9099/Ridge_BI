@extends('admin.layouts.app')
@section('content')
<div id="wrapper">
    <div id="r-content">
        <div class="sp-ma">
            <div class="title-area">
                <h2 class="title">エラー</h2>
            </div>
            @if($error_code == 404)
            <div class="error-content">
                該当するデータが削除済みです
                <div class="go-back-area">
                    <button onclick="history.back()">以前のページへ戻る</button>
                </div>
            </div>

            @endif
        </div>
	</div>
</div>
@endsection
