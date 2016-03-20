@extends('layout.layout-of-index')

@section('content')
	<h1>GatherLinks</h1>
	<pre>GatherLinks is a simple feature from GatherCloud allows you to compose a simple view to share to others.</pre>
	<form action="{{ url('/gtl') }}" method="POST">
		GatherLinks Name: <input type="text" name="lkname" required><br><br>
		Yours Selected items
		<table id="gtl-table" border="1">
			<tr>
                <th class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></th>
                <th class="th-name">Name</th>
                <th class="th-size">Size</th>
                <th class="th-last-mo">Last modified</th>
            </tr>
		</table>
		<br>
		<input type="hidden" name="items" id="post-data">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="Submit" value="Confirm">
	</form>

	<script>

		
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			type: 'GET',
			url: 'create',
			data: {'selected-item': sessionStorage.getItem("selected")},
			success: function(result){
				var table = document.getElementById("gtl-table");
				for (r in result){
					var row = table.insertRow(-1);
					row.insertCell(0).innerHTML = result[r]['provider'];
					row.insertCell(1).innerHTML = result[r]['name'];
					row.insertCell(2).innerHTML = result[r]['size'];
					row.insertCell(3).innerHTML = result[r]['modified'];
				}
				$("#post-data").attr("value",JSON.stringify(result));
			}
		});

	</script>
@endsection