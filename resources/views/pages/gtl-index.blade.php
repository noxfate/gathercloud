@extends('layout.layout-of-index')

@section('content')
	<h1>GatherLinks</h1>
	<br>

	Links: {{ $link->link_name }} <br><br>

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
@endsection