@extends('layouts.master-index')

@section('content')
	<div id="board" class="board">
		{{--<h1>GatherLinks</h1>--}}
		{{--<br>--}}

		{{--Links name: {{ $link[0]->link_name }} <br>--}}

		{{--<button id="geturl-btn" value="{{ url('gtl/shared').'?tokens='.$link[0]->url }}">Get Shareable URL</button>--}}
		{{--<br><br>--}}

		{{--<table border="1">--}}
		{{--<tr>--}}
		{{--<th class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></th>--}}
		{{--<th class="th-name">Name</th>--}}
		{{--<th class="th-size">Size</th>--}}
		{{--<th class="th-last-mo">Last modified</th>--}}
		{{--</tr>--}}
		{{--@foreach($data as $l)--}}
		{{--<tr>--}}
		{{--<td>{{ $l->token_id }}</td>--}}
		{{--<td>{{ $l->name }}</td>--}}
		{{--<td>{{ $l->size }}</td>--}}
		{{--<td>{{ $l->modified }}</td>--}}
		{{--</tr>--}}
		{{--@endforeach--}}
		{{--</table>--}}

		{{--<form method="POST">--}}
		{{--<input type="submit" id="gtl-del-btn" value="Delete">--}}
		{{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
		{{--<input type="hidden" name="_method" value="delete">--}}
		{{--</form>--}}

		{{--<script>--}}
		{{--$("#geturl-btn").click(function(){--}}
		{{--window.prompt("Copy to Clipboard: Press Ctrl+C, Enter", $(this).val());--}}
		{{--});--}}

		{{--$("#gtl-del-btn").click(function(){--}}
		{{--confirm("Are you sure? ** BUG ALERT ** ");--}}
		{{--});--}}
		{{--</script>--}}



		<div class="gtl-box">
			<h4><span class="label label-default" style="color: black">GTL Name</span>&nbsp;<span>{{ $link->link_name }}</span></h4>
			<hr width="50%">
			<div style="margin-bottom: 10px">
				<div style="display: inline-block;">
					<h6 class="text-primary" style="display: inline;padding-right: 3px">Yours Selected items <span class="badge">{{ $data->count() }}</span></h6>
				</div>
				<div style="display: inline-block;border-left: 1px solid #eee;padding-left: 15px;margin-left: 10px">
					<button type="button" id="gtl-url-btn" data-url="{{url('gtl/shared').'?tokens='.$link->url }}" class="btn btn-default btn-sm" aria-hidden="true">
						<span class="glyphicon glyphicon-link"></span> Copy link
					</button>
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
						{{--@if (!empty($data))--}}
						{{--@foreach($data as $d => $val)--}}
						{{--<tr class="withItemMenu" value="{{ $val['path'] }}">--}}
						{{--<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>--}}
						{{--<td class="th-name">--}}
						{{--@if ($val['is_dir'])--}}
						{{--<span class="glyphicon glyphicon-folder-close"></span>--}}
						{{--<a href="{{ Request::getBaseUrl() . "/home/" .$cname . $val['path'] . ($cname == 'all' ? '?in='.$val['connection_name'] : '')}}">--}}
						{{--<span class="dir"--}}
						{{--data-conname="{{ $val['connection_name'] }}"--}}
						{{--value="{{ $val['path'] }}">{{ $val['name'] }}</span>--}}
						{{--</a>--}}
						{{--@else--}}
						{{--<span class="glyphicon glyphicon glyphicon-file"></span>--}}
						{{--<a href="#">--}}
						{{--<span data-conname="{{ $val['connection_name'] }}"--}}
						{{--value="{{ $val['path'] }}">{{ $val['name'] }}</span>--}}
						{{--</a>--}}

						{{--@endif--}}
						{{--</td>--}}
						{{--@if ($val['is_dir']  or ($val['size'] == 0))--}}
						{{--<td class="th-size"></td>--}}
						{{--@else--}}
						{{--<td class="th-size">{{ $val['size'] }}</td>--}}
						{{--@endif--}}
						{{--<td class="th-last-mo">{{ $val['modified'] }}</td>--}}
						{{--<td class="th-action"><span class="caret action"></span></td>--}}
						{{--</tr>--}}
						{{--@endforeach--}}
						{{--@endif--}}
						{{--						@for($i=0 ; $i<3 ; $i++)--}}
						@foreach($data as $index => $d)
							<tr>
								<td class="th-icon-cloud">
									<div class="div-circle-icon">
										<img src="{{ URL::asset('images/logo-provider/'. $logo[$index]) }}">
									</div>
								</td>
								<td class="th-name">
									@if ($d->is_dir)
										<span class="glyphicon glyphicon glyphicon-folder-close"></span>
									@else
										<span class="glyphicon glyphicon glyphicon-file"></span>
									@endif
									<a href="{{$d->shared}}" target="_blank">
										<span>{{ $d->name }}</span>
									</a>
										<br><span class="text-muted font-12">in</span><span class="text-primary font-12">{{$con[$index] . $d->path }}</span>
								</td>
								<td class="th-size">{{ $d->size }}</td>
								<td class="th-last-mo">{{ $d->modified }}</td>
								<td class="th-action"><span class="caret action"></span></td>
							</tr>
						@endforeach
						{{--@endfor--}}
					</table>
				</div>
			</div>
			<br>
			{{--<button class="btn btn-default"  id="gtl-edit-btn">Edit</button>--}}
			<form action="" method="POST">
				{{--<button class="btn btn-danger"  id="gtl-delete-btn">Delete</button>--}}
				<input type="submit" class="btn btn-danger" name="delBtn" value="Delete">
				{{ method_field("DELETE") }}
				<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
			</form>
		</div>
	</div>
	<script>

		document.getElementById('tab-drives').className = "";
		document.getElementById('tab-gtls').className = "active";
		document.getElementById('content-drives').className = "tab-pane fade";
		document.getElementById('content-gtls').className = "tab-pane fade active in";
		document.getElementById('side-bar-select-{{ $key }}').className = "withSelect";

		$("#gtl-url-btn").click(function (e) {
			window.prompt("Copy to Clipboard: Press Ctrl+C, Enter", $(this).attr("data-url"));
		});
	</script>
@endsection