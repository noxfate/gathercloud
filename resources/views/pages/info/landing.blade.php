<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootswatch.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/landing.css')}}">
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
</head>
<body>
<header>
    <div id="logo" class="land-logo"><img src="{{URL::asset('images/logo-gtc.png')}}" ></div>
    <div class="land-menu">
        <ul>
            <li><a href="#">About</a></li>
            <li><a href="#">Our Story</a></li>
        </ul>
    </div>
</header>
<span class="v"></span><div class="board-land">
    <div class="web-name">GatherCloud</div>
    <div class="qoute">Single gateway for Cloud Storages</div>
    <div class="join">
        <a href="{{ url('/login') }}">Try it</a>
        <a href="{{ url('/register') }}">Register</a>
    </div>
</div>
<footer>
    Footer
</footer>
</body>
</html>
