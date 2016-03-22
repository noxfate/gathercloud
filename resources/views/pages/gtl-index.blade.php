@extends('layout.layout-of-index')

@section('content')
	<h1>GatherLinks</h1>
	<br>

	Links name: {{ $link->link_name }} <br>

	<button id="geturl-btn" value="{{ url('gtl/shared').'?tokens='.$link->url }}">Get Shareable URL</button>
	<br><br>

	<table border="1">
		<tr>
            <th class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></th>
            <th class="th-name">Name</th>
            <th class="th-size">Size</th>
            <th class="th-last-mo">Last modified</th>
        </tr>
        @foreach(json_decode($link->data) as $l)
        <tr>
        	<td>{{ $l->provider }}</td>
        	<td>{{ $l->name }}</td>
        	<td>{{ $l->size }}</td>
        	<td>{{ $l->modified }}</td>
        </tr>
        @endforeach
	</table>

	<form method="POST">
		<input type="submit" id="gtl-del-btn" value="Delete">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="delete">
	</form>

	<script>
		$("#geturl-btn").click(function(){
			window.prompt("Copy to Clipboard: Press Ctrl+C, Enter", $(this).val());
		});

		$("#gtl-del-btn").click(function(){
			confirm("Are you sure? ** BUG ALERT ** ");
		});
	</script>
@endsection