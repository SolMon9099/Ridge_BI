@extends('admin.layouts.auth')

@section('content')
<div class="login-wrap">
        <h1 id="logo"><img src="{{ asset('assets/admin/img/logo-top.svg') }}" alt=""></h1>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <input type="text" class="username" placeholder="メールアドレス" name="email">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror

            <input type="password" class="password" placeholder="パスワード" name="password">
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror

            <input type="submit"  value="ログイン" class="login">

			<!--<p class="forgetpassword">パスワードを忘れた方は<a href="forgetpassword.html">コチラ</a></p>
            <p class="register"><a href="firsttime.html">会員登録</a></p>-->
        </form>

    </div>
@endsection
