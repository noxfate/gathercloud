<input type="hidden" id="ajr" data-getStorages="{{URL::route("getStorages")}}"
       data-redundancyCheck="{{URL::route("redundancyCheck")}}"
       data-getFolderList="{{URL::route("getFolderList")}}"
       data-getConnectionList="{{URL::route("getConnectionList")}}">
<script>
    document.getElementById('side-bar-select-{{ $cname }}').className = "withSelect";
</script>
<div id="box-st-bar" class="box-st-bar">
    <div id="create-bar" class="create-bar">
        <button id="trig-new-folder" class="btn btn-default btn-sm create-btn" data-toggle="modal" data-target="#modal-new-folder">
            {{--<div class="icon-new-folder"></div>--}}
            {{--New Folder--}}
            <span class="glyphicon glyphicon-plus-sign"></span>
            New Folder
        </button>
        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" id="modal-new-folder" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-upload" role="document">
                <!-- Modal content-->
                <form action="{{ url('createFolder') }}" target="hiddenIframe" method="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="col-lg-2 control-label" style="padding: 5px 0px 0px 0px;">
                                    <h5 style="margin: 0px;"><span class="label label-primary">Name</span></h5>
                                </label>
                                <div class="col-lg-7" style="margin-left: 25px;padding: 0px;">
                                    <input type="text" name="name" required class="form-control" style="font-size: 18px" placeholder="name">
                                </div>
                            </div>
                            <input type="hidden" name="destination" value="{{$parent->par_now}}"/>
                            <input type="hidden" name="connection_name" value="{{$in}}"/>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-primary" id="btn-upload" value="Create">
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        @if($cname == 'all')
                <!-- Btn trigger modal Upload -->
        <button id="trig-upload" class="btn btn-default btn-sm create-btn" data-toggle="modal" data-target="#all-modal-upload">
            <span class="glyphicon glyphicon-cloud-upload"></span>
            File Upload
        </button>
        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" id="all-modal-upload" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-upload" role="document">
                <!-- Modal content-->
                <form action="{{ url('upload-dummy') }}" id="upload_form" target="hiddenIframe" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload File</h4>
                        </div>
                        <div class="modal-body">
                            <div class="fileUpload btn btn-primary">
                                <span>Choose File</span>
                                <input type="file" id="file" name="file" class="upload" />
                            </div>
                            <input type="hidden" name="dummy_path" value="{{$parent->par_now}}"/>
                            <input type="hidden" name="dummy_store" value="{{$in}}"/>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <span id="file-selected" style="padding-left: 5px">No file chosen</span>
                            <h6 id="rdd-text" class="displayNone">
                                <div class="loader">
                                    <span></span><span></span><span></span><span></span>
                                </div>
                                Redundancy checking...
                                {{--<span class="glyphicon glyphicon-ok-sign rdd-success" aria-hidden="true"></span>--}}
                                {{--File doesn't exist in this drive.--}}
                            </h6>

                            <div class="panel panel-primary displayNone" id="panel-priority">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Please select your storage.</h3>
                                </div>
                                <div class="panel-body priority thin-scrollbar" id="priority-storages">

                                    {{--<div class="radio">--}}
                                        {{--<label>--}}
                                            {{--<input type="radio" name="real_store" id="optionsRadios1" value="db2" checked="">--}}
                                            {{--<div class="limit-text">{{ 'box text 2 '}}</div> <p class="text-muted">{{'2.5GB free of 2.5GB'}}</p>--}}
                                        {{--</label>--}}
                                        {{--<div id="bar-2" class="bar-main-container azure">--}}
                                            {{--<div class="wrap">--}}
                                                {{--<div class="bar-percentage" data-percentage="81"></div>--}}
                                                {{--<div class="bar-container">--}}
                                                    {{--<div class="bar"></div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-primary" id="btn-upload" value="Upload">
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        @else
                <!-- Btn trigger modal Upload -->
        <button id="trig-upload" class="btn btn-default btn-sm create-btn" data-toggle="modal" data-target="#modal-upload">
            <span class="glyphicon glyphicon-cloud-upload"></span>
            File Upload
        </button>
        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" id="modal-upload" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-upload" role="document">
                <!-- Modal content-->
                <form action="{{ url('upload') }}" id="upload_form" target="hiddenIframe" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload</h4>
                        </div>
                        <div class="modal-body">
                            <div class="fileUpload btn btn-primary">
                                <span>Choose File</span>
                                <input type="file" id="file" name="file" class="upload" />
                            </div>
                            <input type="hidden" name="destination" value="{{$parent->par_now}}"/>
                            <input type="hidden" name="connection_name" value="{{$in}}"/>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <span id="file-selected" style="padding-left: 5px">No file chosen</span>
                            <h6 id="rdd-text"><span class="glyphicon glyphicon-ok-sign rdd-success" aria-hidden="true"></span> not have file in drive</h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-primary" id="btn-upload" value="Upload">
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        @endif
    </div>
    <div id="search-bar" class="search-bar">
        <form action="{{ url("/search/{$cname}") }}">
            <div class="input-group">
                <input class="form-control input-sm" type="text" name="keyword" required placeholder="Search for...">
                <span class="input-group-btn"><button class="btn btn-default btn-sm go-search" type="submit">Go</button></span>
            </div>
        </form>
    </div>
</div>
<div id="box-nd-bar" class="box-nd-bar">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div id="nav-bar" class="nav-bar">
                @if (!empty($parent))
                    <ul class="breadcrumb">
                        @for ($i = 0; $i < count($parent->par_name); $i++)
                            <li>
                                @if ($i == 0)
                                    {{--{{$parent->par_now}}--}}
                                    <a href="{{ url("/home{$parent->par_path[$i]}") }}">{{ $parent->par_name[$i] }}</a>
                                @else
                                    <a href="{{ url("/home{$parent->par_path[$i]}?in={$in}") }}">{{ $parent->par_name[$i] }}</a>
                                @endif
                            </li>
                        @endfor
                    </ul>
                @endif
            </div>
        </div>
        <div class="panel-body">
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
                                            <span class="dir" data-conname="{{ $val['connection_name'] }}" value="{{ $val['path'] }}">{{ $val['name'] }}</span>
                                            <br><span class="text-muted font-12">in</span><a href="#"><span class="text-primary font-12">{{"/".$val['connection_name']. $val['path'] }}</span></a>
                                        </a>
                                    @else
                                        <span class="glyphicon glyphicon glyphicon-file"></span>
                                        <a href="#">
                                            <span data-conname="{{ $val['connection_name'] }}" value="{{ $val['path'] }}">{{ $val['name'] }}</span>
                                            <br><span class="text-muted font-12">in</span><a href="#"><span class="text-primary font-12">{{ "/".$val['connection_name']. $val['path'] }}</span></a>
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
                    {{--@for($i=0 ; $i<30 ; $i++)--}}
                    {{--<tr class="withItemMenu">--}}
                    {{--<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>--}}
                    {{--<td class="th-name">--}}
                    {{--<span class="glyphicon glyphicon glyphicon-file"></span>--}}
                    {{--<a href="#">--}}
                    {{--<span>{{'File ' . $i}}</span>--}}
                    {{--</a>--}}
                    {{--</td>--}}
                    {{--<td class="th-size">1 GB</td>--}}
                    {{--<td class="th-last-mo">2016 12 13 20:00:01</td>--}}
                    {{--<td class="th-action"><span class="caret action"></span></td>--}}
                    {{--</tr>--}}
                    {{--@endfor--}}
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rename-->
<div class="modal fade bs-example-modal-lg" id="modal-rename" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <form action="{{ url('rename') }}" target="hiddenIframe" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Rename</h4>
                </div>
                <div class="modal-body">
                    <input type='text' id='new_name' name='new_name'>
                    <input type="hidden" id="rename_file" name="file">
                    <input type="hidden" id="rename_connection" name="connection_name">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-rename" >Save</button>
                    {{--<button class="btn btn-primary" id="create-copy" value="Upload">--}}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal Transfer-->
<div class="modal fade bs-example-modal-lg" id="modal-transfer" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <form action="{{ url('transferFile') }}" target="hiddenIframe" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Transfer to another drive.</h4>
                </div>
                <div class="modal-body transfer-modal-body">
                    {{--<div class="btn-group" data-toggle="buttons">--}}
                        {{--<label class="btn btn-link">--}}
                            {{--<input type="radio" name="options" id="myoption1">Radio-One--}}
                        {{--</label>--}}
                        {{--<label class="btn btn-warning">--}}
                            {{--<input type="radio" name="options" id="myoption2">Radio-Two--}}
                        {{--</label>--}}
                        {{--<label class="btn btn-warning">--}}
                            {{--<input type="radio" name="options" id="myoption3">Radio-Three--}}
                        {{--</label>--}}
                    {{--</div>--}}
                    <div class="transfer-box">
                        <div id="transfer-box" class="btn-group" data-toggle="buttons">
                        {{--<ul class="ul-transfer first-node">--}}
                            {{--<li>--}}
                        {{--<span class="glyphicon glyphicon-minus-sign gg-margin-right-5"></span>--}}
                        {{--<span><span class="glyphicon glyphicon-cloud gg-margin-right-4"></span>box test 2</span>--}}
                                {{--<ul class="ul-transfer">--}}
                                    {{--<li>--}}
                                        {{--<span class="glyphicon glyphicon-minus-sign gg-margin-right-5"></span>--}}
                                        {{--<span>Test 1</span>--}}
                                        {{--<ul class="ul-transfer">--}}
                                            {{--<li>--}}
                                                {{--<span class="glyphicon glyphicon-plus-sign gg-margin-right-5"></span>--}}
                                                {{--<span>lvl1</span>--}}
                                            {{--</li>--}}
                                            {{--<li class="transfer-select">--}}
                                                {{--<span class="glyphicon glyphicon-plus-sign gg-margin-right-5"></span>--}}
                                                {{--<span class="text-primary">SMO_TEST</span>--}}
                                            {{--</li>--}}
                                        {{--</ul>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    </div>
                    </div>
                    <input type="hidden" name="tf_file" id="tf_file">
                    <input type="hidden" name="from_connection" id="from_connection">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-rename" >Transfer</button>
                    {{--<button class="btn btn-primary" id="create-copy" value="Upload">--}}

                </div>
            </div>
        </form>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

{{--<iframe name="hiddenIframe" id="hiddenIframe" style="display: none;" ></iframe>--}}
<iframe name="hiddenIframe" id="hiddenIframe"  ></iframe>
<span class="loading style-1"></span>
<div class="loader">
    <h1></h1><span></span><span></span><span></span>
</div>

<script src="{{ URL::asset('js/index-board.script.js') }}"></script>
@include("components.contextmenu")
