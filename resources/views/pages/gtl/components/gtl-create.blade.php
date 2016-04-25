<div class="gtl-box">
	<form action="{{ url('/gtl') }}" method="POST" style="height: 100%">
		<h4><span class="label label-default" style="color: black">GTL Name</span>&nbsp;<span>test gtl</span></h4>
		<hr width="50%">
		<div style="height: 35px">
			<div style="display: inline-block;">
				<h6 class="text-primary" style="display: inline;padding-right: 3px">Yours Selected items <span class="badge">3</span></h6>
			</div>
		</div>
		<div class="gtl-div-table">
			<table id="table-header" class="table-header">
				<tr>
					<th class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></th>
					<th class="th-name">Name</th>
					<th class="th-size">Size</th>
					<th class="th-last-mo">Last modified</th>
					<th class="th-action"></th>
				</tr>
			</table>
			<div id="board-body" class="board-body thin-scrollbar">
				<table class="table-body table-hover table-striped">
					@if (!empty($data))
						@foreach($data as $d => $val)
							<tr class="withItemMenu" value="{{ $val['path'] }}">
								<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
								<td class="th-name">
									@if ($val['is_dir'])
										<span class="glyphicon glyphicon-folder-close"></span>
										<a href="{{ Request::getBaseUrl() . "/home/" .$cname . $val['path'] . ($cname == 'all' ? '?in='.$val['connection_name'] : '')}}">
<span class="dir"
	  data-conname="{{ $val['connection_name'] }}"
	  value="{{ $val['path'] }}">{{ $val['name'] }}</span>
										</a>
									@else
										<span class="glyphicon glyphicon glyphicon-file"></span>
										<a href="#">
<span data-conname="{{ $val['connection_name'] }}"
	  value="{{ $val['path'] }}">{{ $val['name'] }}</span>
										</a>

									@endif
								</td>
								@if ($val['is_dir']  or ($val['size'] == 0))
									<td class="th-size"></td>
								@else
									<td class="th-size">{{ $val['size'] }}</td>
								@endif
								<td class="th-last-mo">{{ $val['modified'] }}</td>
								<td class="th-action"><span class="caret action"></span></td>
							</tr>
						@endforeach
					@endif
					@for($i=0 ; $i<3 ; $i++)
						<tr class="withItemMenu">
							<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
							<td class="th-name">
								<span class="glyphicon glyphicon glyphicon-file"></span>
								<a href="#">
									<span>{{'File ' . $i}}</span>
								</a>
							</td>
							<td class="th-size">1 GB</td>
							<td class="th-last-mo">2016 12 13 20:00:01</td>
							<td class="th-action"><span class="caret action"></span></td>
						</tr>
					@endfor
				</table>
			</div>
			<input type="hidden" name="items" id="post-data">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
		</div>
		<br>
		<input class="btn btn-primary" type="Submit" onclick="sessionStorage.removeItem('selected')" id="gtl-create-btn" value="Confirm">
	</form>
</div>


{{--<h1>GatherLinks</h1>--}}
{{--<pre>GatherLinks is a simple feature from GatherCloud allows you to compose a simple view to share to others.</pre>--}}
{{--<form action="{{ url('/gtl') }}" method="POST">--}}
{{--GatherLinks Name: <input type="text" name="lkname" required><br><br>--}}
{{--Yours Selected items--}}
{{--<table id="gtl-table" border="1">--}}
{{--<thead>--}}
{{--<tr>--}}
{{--<th class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></th>--}}
{{--<th class="th-name">Name</th>--}}
{{--<th class="th-size">Size</th>--}}
{{--<th class="th-last-mo">Last modified</th>--}}
{{--</tr>--}}
{{--</thead>--}}
{{--<tfoot>--}}

{{--</tfoot>--}}
{{--</table>--}}
{{--<br>--}}
{{--<input type="hidden" name="items" id="post-data">--}}
{{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
{{--<input type="Submit" onclick="sessionStorage.removeItem('selected')" id="gtl-create-btn" value="Confirm">--}}
{{--</form>--}}

{{--<script>--}}
{{--$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});--}}
{{--$.ajax({--}}
{{--type: 'GET',--}}
{{--url: 'gtl/create',--}}
{{--data: {'selected-item': sessionStorage.getItem("selected")},--}}
{{--success: function(result){--}}
{{--var table = document.getElementById("gtl-table");--}}
{{--var ids = [];--}}
{{--for (r in result){--}}
{{--var row = table.insertRow(-1);--}}
{{--row.insertCell(0).innerHTML = result[r]['token_id'];--}}
{{--row.insertCell(1).innerHTML = result[r]['name'];--}}
{{--row.insertCell(2).innerHTML = result[r]['size'];--}}
{{--row.insertCell(3).innerHTML = result[r]['modified'];--}}
{{--ids.push(result[r]['id']);--}}
{{--}--}}
{{--//                alert(ids);--}}
{{--$("#post-data").attr("value",JSON.stringify(ids));--}}
{{--}--}}
{{--});--}}
//        $("#gtl-table").DataTable({
//            ajax: {
//                type: 'GET',
//                url: 'gtl/create',
//                data: {'selected-item': sessionStorage.getItem("selected")},
//                success: function(result){
//                    $("#post-data").attr("value",JSON.stringify(result));
//                    sessionStorage.removeItem("selected");
//                }
//            },
//            columns: [
//                {data: '0.token_id'},
//                {data: '0.name'},
//                {data: '0.size'},
//                {data: '0.modified'}
//            ]
//        });


{{--</script>--}}
{{--<script src="{{ URL::asset('js/jquery.min.js') }}"></script>--}}
