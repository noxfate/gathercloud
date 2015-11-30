@extends("layout")

@section("content")

    <form action="register" method="POST">
        Name : <input type="text" name="name"><br>
        E-mail : <input type="email" name="email"><br>
        Password : <input type="password" name="pwd"><br>
        Re-Password : <input type="password" name="repwd"><br>
        {{--<input type="hidden" name="_method" value="PUT">--}}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" value="Register">
    </form>

@endsection