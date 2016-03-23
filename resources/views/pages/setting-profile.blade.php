
<!-- Change to layouts.layouts-of-setting bar -->
@extends('layouts.master-index')
@section('content')
	<div id="board-setting">
		<h1>Profile Setting</h1>
		<form action="{{ url('/setting/profile/'.$user->id) }}" method="POST">
			First Name: <input type="text" name="fname" value="{{ $user->first_name }}"> <br>
			Last Name: <input type="text" name="lname" value="{{ $user->last_name }}"> <br>
			E-mail: {{ $user->email }} <br>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="menu" value="prof">
			<input type="submit" value="Save">
		</form>

		<br>
		==========================================
		<br>
		<h1> Password Setting</h1>
		<form action="{{ url('/setting/profile/'.$user->id) }}" method="POST">
			Old Password: <input type="password" name="old_pwd"> <br><br>
			New Password: <input type="password" name="new_pwd"> <br>
			Re-Enter Password: <input type="password" name="re_pwd"> <br>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="menu" value="pwd">
            <input type="submit" value="Save">
		</form>
	</div>

@endsection