{{--<iframe name="hiddenIframe" id="hiddenIframe" style="display: none;" ></iframe>--}}
<iframe name="hiddenIframe" id="hiddenIframe"  ></iframe>
<div id="box-nav-bar" class="box-nav-bar">
    <div id="nav-bar" class="nav-bar">
        @if (!empty($parent))
            @for ($i = 0; $i < count($parent->par_name); $i++)
                @if ($i == 0)
                    {{--{{$parent->par_now}}--}}
                    <a href="{{ url("/home{$parent->par_path[$i]}") }}"><span>{{ $parent->par_name[$i] }}</span></a>
                @else
                    <span class="glyphicon glyphicon-menu-right"></span>
                    <span class="glyphicon glyphicon-folder-open"></span>
                    <a href="{{ url("/home{$parent->par_path[$i]}?in={$in}") }}"><span>{{ $parent->par_name[$i] }}</span></a>
                @endif
            @endfor
        @endif
    </div>
    <div id="create-bar" class="create-bar">
        <button id="new-folder" class="btn btn-default">
            <div class="icon-new-folder"></div>
            New Folder
        </button>
        @if($cname == 'all')
        <!-- Btn trigger modal Upload -->
        <button id="trig-upload" class="btn btn-default" data-toggle="modal" data-target="#all-modal-upload">
            <span class="glyphicon glyphicon-cloud-upload"></span>
            File Upload
        </button>
        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" id="all-modal-upload" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <!-- Modal content-->
                <form action="{{ url('upload-dummy') }}" target="hiddenIframe" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload</h4>
                        </div>
                        <div class="modal-body">
                            <input type="file" name="file">
                            <input type="hidden" name="dummy_path" value="{{$parent->par_now}}">
                            <input type="hidden" name="dummy_store" value="{{$in}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            @foreach ($upload_storages as $c)
                                <input type="radio" name="real_store" value="{{ $c->connection_name }}">{{ $c->connection_name }}
                                <br>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-primary" id="btn-upload" value="Upload">
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
        @else
        <!-- Btn trigger modal Upload -->
        <button id="trig-upload" class="btn btn-default" data-toggle="modal" data-target="#modal-upload">
            <span class="glyphicon glyphicon-cloud-upload"></span>
            File Upload
        </button>
        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" id="modal-upload" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <!-- Modal content-->
                <form action="{{ url('upload') }}" target="hiddenIframe" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload</h4>
                        </div>
                        <div class="modal-body">
                            <input type="file" name="file">
                            <input type="hidden" name="destination" value="{{$parent->par_now}}">
                            <input type="hidden" name="connection_name" value="{{$cname}}" >
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-primary" id="btn-upload" value="Upload">
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
        @endif
        <form action="{{ url("/search/{$cname}") }}">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search for...">
                      <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">Go!</button>
                      </span>
            </div>
        </form>


    </div>
</div>

<table id="table-header" class="table-header">
    <tr>
        <th class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></th>
        <th class="th-name">Name</th>
        <th class="th-size">Size</th>
        <th class="th-last-mo">Last modified</th>
        <th class="th-action"></th>
    </tr>
</table>
<div id="board-body" class="board-body">
    <table class="table-body table-hover table-striped">
        <script>
            document.getElementById('side-bar-select-{{ $cname }}').className = "withSelect";
        </script>
        @if (!empty($data))
            @foreach($data as $d => $val)
                <tr class="withItemMenu" value="{{ $val['path'] }}">
                    <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>

                    {{--<td class="th-icon-cloud"><input class="gtl-chkbox" id="{{ $d }}" type="checkbox"></td>--}}

                    <td class="th-name">
                        @if ($val['is_dir'])
                            <span class="glyphicon glyphicon-folder-close"></span>
                            <a href="{{ Request::getBaseUrl() . "/home/" .$cname . $val['path'] . ($cname == 'all' ? '?in='.$val['conName'] : '')}}">
                                <span class="dir"
                                      data-conname="{{ $val['conName'] }}" data-tokenid="{{$val['token_id']}}"
                                      value="{{ $val['path'] }}">{{ $val['name'] }}</span>
                            </a>
                        @else
                            <span class="glyphicon glyphicon glyphicon-file"></span>
                            <a href="#">
                                <span data-conname="{{ $val['conName'] }}" data-tokenid="{{$val['token_id']}}"
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
    </table>
    {{--<button id="gtl-btn-save">Save</button>  <button id="gtl-btn-cancel">Cancel</button>--}}
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
