@extends('layout')

@section('content')
   <h1>Landing Page</h1>

    <form action="login" method="POST">
        E-mail : <input type="email" name="email">
        Password: <input type="password" name="pwd">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" value="submit">

    </form>
    <a href="register">Register</a><br>
@endsection