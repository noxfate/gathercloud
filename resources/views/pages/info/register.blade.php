
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('bootswatch.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/register.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-add.css')}}">
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
</head>
<body>
<div class="regis-bg">
    <div class="regis">
        <div class="regis-logo"><h1>&lt;Logo&gt;</h1></div>
    <form action="{{ url('/register') }}" method="POST">
        First Name : <span class="glyphicon glyphicon-pencil"></span><input type="text" name="fname" required>
        Last Name : <span class="glyphicon glyphicon-pencil last"></span><input type="text" name="lname" required>
        E-mail : <span class="glyphicon glyphicon-envelope"></span><input type="email" name="email" required>
        Password : <span class="glyphicon glyphicon-lock"></span><input type="password" name="pwd" required>
        Re-Password : <span class="glyphicon glyphicon-lock re"></span><input type="password" name="repwd" required>
        {{--<input type="hidden" name="_method" value="PUT">--}}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" value="Register">
    </form>
    </div>
</div>
</body>
</html>

