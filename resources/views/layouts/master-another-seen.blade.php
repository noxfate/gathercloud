<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gathercloud</title>
    @include('components.main_script')
</head>
<body>

<div id="top-bar" class="top-bar">
    <div id="logo" class="logo"><img src="{{URL::asset('images/logo-mini-white-gtc.png')}}"></div>
</div>

<div class="box-lv-1">
    @yield("content")
</div>
</body>
</html>