<iframe name="hiddenIframe" id="hiddenIframe" style="display: none;" ></iframe>
{{--<iframe name="hiddenIframe" id="hiddenIframe"  ></iframe>--}}
<script>
    document.getElementById('side-bar-select-{{ $cname }}').className = "withSelect";
</script>
<div id="box-st-bar" class="box-st-bar">
    <div id="create-bar" class="create-bar">
        <button id="new-folder" class="btn btn-default btn-sm create-btn">
            {{--<div class="icon-new-folder"></div>--}}
            {{--New Folder--}}
            <span class="glyphicon glyphicon-plus-sign"></span>
            New Folder
        </button>
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
                <form action="{{ url('upload-dummy') }}" target="hiddenIframe" method="POST" enctype="multipart/form-data">
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
                            <h6 id="rdd-text"><span class="glyphicon glyphicon-ok-sign rdd-success" aria-hidden="true"></span> not have file in drive</h6>

                            <div class="panel panel-primary" id="panel-priority" style="display: none">
                                <div class="panel-heading">
                                    <h3 class="panel-title">You can select storage</h3>
                                </div>
                                <div class="panel-body priority thin-scrollbar">
                                    {{--@foreach ($upload_storages as $c)--}}
                                    {{--<div class="radio">--}}
                                    {{--<label>--}}
                                    {{--<input type="radio" name="real_store" id="optionsRadios1" value="{{ $c->connection_name }}" checked="">--}}
                                    {{--{{ $c->connection_name }}--}}
                                    {{--</label>--}}
                                    {{--</div>--}}
                                    {{--@endforeach--}}
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="real_store" id="optionsRadios1" value="" checked="">
                                            <div class="limit-text">{{ 'Project Drive sdfsdfsdifhsidfhskdjfhsdfhsidfhsdohfsf'}}</div> <p class="text-muted">{{'15GB free of 20GB'}}</p>
                                        </label>
                                        <div id="bar-2" class="bar-main-container azure">
                                            <div class="wrap">
                                                <div class="bar-percentage" data-percentage="38"></div>
                                                <div class="bar-container">
                                                    <div class="bar"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="real_store" id="optionsRadios1" value="">
                                            <b>{{ 'ma books ' }}</b> <p class="text-muted">{{'15GB free of 20GB'}}</p>
                                        </label>
                                        <div id="bar-2" class="bar-main-container yellow">
                                            <div class="wrap">
                                                <div class="bar-percentage" data-percentage="71"></div>
                                                <div class="bar-container">
                                                    <div class="bar"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="real_store" id="optionsRadios1" value="" >
                                            <b>{{ 'dropbox for IT '}}</b> <p class="text-muted">{{'15GB free of 20GB'}}</p>
                                        </label>
                                        <div id="bar-2" class="bar-main-container red">
                                            <div class="wrap">
                                                <div class="bar-percentage" data-percentage="85"></div>
                                                <div class="bar-container">
                                                    <div class="bar"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                <form action="{{ url('upload') }}" target="hiddenIframe" method="POST" enctype="multipart/form-data">
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
                            <input type="hidden" name="dummy_path" value="{{$parent->par_now}}"/>
                            <input type="hidden" name="dummy_store" value="{{$in}}"/>
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
                                    <a href="{{ url("/home{$parent->par_path[$i]}") }}"><span>{{ $parent->par_name[$i] }}</span></a>
                                @else
                                    <span class="glyphicon glyphicon-menu-right"></span>
                                    <span class="glyphicon glyphicon-folder-open"></span>
                                    <a href="{{ url("/home{$parent->par_path[$i]}?in={$in}") }}"><span>{{ $parent->par_name[$i] }}</span></a>
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
                    @for($i=0 ; $i<30 ; $i++)
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

<script src="{{ URL::asset('js/index-board.script.js') }}"></script>
@include("components.contextmenu")
