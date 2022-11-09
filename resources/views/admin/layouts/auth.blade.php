<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('assets/admin/img/title_favicon.ico?2') }}">

     <!-- Styles -->
     <link href="{{ asset('assets/admin/css/basic.css') }}?{{ Carbon::now()->format('Ymdhis') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/top.css') }}?{{ Carbon::now()->format('Ymdhis') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/common.css') }}?{{ Carbon::now()->format('Ymdhis') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/modal.css') }}?{{ Carbon::now()->format('Ymdhis') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('assets/admin/js/jquery.js') }}?{{ Carbon::now()->format('Ymdhis') }}" ></script>
    <script src="{{ asset('assets/admin/js/modal.js') }}?{{ Carbon::now()->format('Ymdhis') }}" ></script>
    <script src="{{ asset('assets/admin/js/customize.js') }}?{{ Carbon::now()->format('Ymdhis') }}" ></script>
    <script src="{{ asset('assets/admin/js/ajaxzip3.js') }}?{{ Carbon::now()->format('Ymdhis') }}" ></script>

</head>
<body>
    <div id="top">
        @yield('content')
    </div>
</body>
</html>
