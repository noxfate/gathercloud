
<!-- Change to layout.layout-of-setting bar -->
@extends('layout.layout-of-index')
@section('content')
	<div id="board-setting">
		<h1> Profile Setting</h1>
		<form action="{{ url('/setting/profile/edit') }} ">
			First Name: <input type="text" name="fname" value="{{ $user->first_name }}"> <br>
			Last Name: <input type="text" name="lname" value="{{ $user->last_name }}"> <br>
			E-mail: {{ $user->email }} <br>
			<input type="submit" value="Save">
		</form>

		<br>
		==========================================
		<br>
		<h1> Password Setting</h1>
		<form action="">
			Old Password: <input type="password" name="old_pwd"> <br><br>
			New Password: <input type="password" name="new_pwd"> <br>
			Re-Enter Password: <input type="password" name="re_pwd"> <br>
			<input type="submit" value="Save">
		</form>
	</div>


	<script type="text/javascript">

	
		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });


	</script>

@endsection