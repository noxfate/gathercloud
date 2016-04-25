<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootswatch.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-add.css')}}">
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
</head>
<body>
<div class="login-bg">
    <div class="login">
        <div class="login-logo"><img src="{{URL::asset('images/logo-gtc.png')}}" height="100px"></div>
        <form action="{{ url('login') }}" method="POST">
            E-mail: <span class="glyphicon glyphicon-envelope"></span><input type="email" id="email" name="email" required>
            Password: <span class="glyphicon glyphicon-lock"></span><input type="password" id="pwd" name="pwd"  required>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="submit" value="login">

        </form>
    </div>
</div>
<script>
</script>
</body>
</html>
