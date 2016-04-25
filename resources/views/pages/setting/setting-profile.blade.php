
<!-- Change to layouts.layouts-of-setting bar -->
@extends('layouts.master-index')
@section('content')
	<div id="board-setting" class="board">
		<div class="setting-box">
			<form action="{{ url('/setting/profile/'.$user->id) }}" method="POST" class="form-horizontal">
				<fieldset>
					<legend><h4><span class="label label-primary">Profile Setting</span></h4></legend>
					<div class="form-group">
						<label for="fn" class="col-lg-2 control-label">First Name:</label>
						<div class="col-lg-4 has-success">
							<input type="text" class="form-control" id="fn" name="fname" value="{{ $user->first_name }}">
						</div>
					</div>
					<div class="form-group">
						<label for="ln" class="col-lg-2 control-label">Last Name:</label>
						<div class="col-lg-4 has-success">
							<input type="text" class="form-control" id="ln" name="lname" value="{{ $user->last_name }}">
						</div>
					</div>
					<div class="form-group">
						<label for="em" class="col-lg-2 control-label">E-mail:</label>
						<div class="col-lg-4">
							<input type="text" disabled="" class="form-control" id="em" name="email" value="{{ $user->email }}">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label"></label>
						<div class="col-lg-4" style="margin-top: 10px">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="_method" value="PUT">
							<input type="hidden" name="menu" value="prof">
							<input type="submit" value="Save" class="btn btn-primary">
						</div>
					</div>
				</fieldset>
			</form>
			<form action="{{ url('/setting/profile/'.$user->id) }}" method="POST" class="form-horizontal">
				<fieldset>
					<legend><h4><span class="label label-primary">Password Setting</span></h4></legend>
					<div class="form-group">
						<label for="op" class="col-lg-2 control-label">Old Password:</label>
						<div class="col-lg-4">
							<input type="text" class="form-control" id="op" type="password" name="old_pwd">
						</div>
					</div>
					<div class="form-group">
						<label for="ln" class="col-lg-2 control-label">New Password:</label>
						<div class="col-lg-4">
							<input type="text" class="form-control" id="ln" name="new_pwd">
						</div>
					</div>
					<div class="form-group">
						<label for="ln" class="col-lg-2 control-label">Re-Enter Password:</label>
						<div class="col-lg-4">
							<input type="text" class="form-control" id="ln" name="re_pwd">
						</div>
					</div>
					<div class="form-group">
						<label for="ln" class="col-lg-2 control-label"></label>
						<div class="col-lg-4" style="margin-top: 10px">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="_method" value="PUT">
							<input type="hidden" name="menu" value="prof">
							<input type="submit" value="Save" class="btn btn-primary">
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>

@endsection