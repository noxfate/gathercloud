<form action="{{ url('/upload') }}" method="POST" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" value="Upload">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>