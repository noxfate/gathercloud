@extends("layout.layout-of-login-register")

@section("content")

    <form action="{{ url('/register') }}" method="POST">
        First Name : <input type="text" name="fname"><br>
        Last Name : <input type="text" name="lname"><br>
        E-mail : <input type="email" name="email"><br>
        Password : <input type="password" name="pwd"><br>
        Re-Password : <input type="password" name="repwd"><br>
        {{--<input type="hidden" name="_method" value="PUT">--}}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" value="Register">
    </form>

@endsection