<form action="{{ url('login') }}" method="POST">
    E-mail: <input type="email" name="email">
    Password: <input type="password" name="pwd">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="submit" value="login">

</form>