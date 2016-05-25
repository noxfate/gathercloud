<div id="p-loading" class="p-loading displayNone">
    <div>
        <div class="c1"></div>
        <div class="c2"></div>
        <div class="c3"></div>
        <div class="c4"></div>
    </div>
    <span>loading</span>
</div>

<input type="hidden" id="ajr" data-getStorages="{{URL::route("getStorages")}}"
       data-redundancyCheck="{{URL::route("redundancyCheck")}}"
       data-getFolderList="{{URL::route("getFolderList")}}"
       data-getConnectionList="{{URL::route("getConnectionList")}}"
        data-rename="{{URL::route("rename")}}"
        data-createFolder="{{URL::route("createFolder")}}"
       data-transferFile="{{URL::route("transferFile")}}"
        data-upload="{{URL::route("upload")}}"
       data-upload-dummy="{{URL::route("upload-dummy")}}"
        data-checkStorage="{{URL::route("checkStorage")}}"
        data-getLink="{{URL::route("getLink")}}"
        data-copy="{{URL::route("copy")}}"
        data-move="{{URL::route("move")}}">
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
                <form id="form-createFolder" action="{{ url('createFolder') }}" target="hiddenIframe" method="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload</h4>
                            <p>in {{ $parent->par_now == "" ? 'Root' : $in.'/'.$parent->par_now}}</p>
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
                            <input type="button" onclick="createFolderSubmit()" class="btn btn-primary" id="btn-upload" value="Create">
                            {{--onclick="createFolderSubmit()"--}}
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
                <form id="form-upload-dummy" action="{{ url('upload-dummy') }}" target="hiddenIframe" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload File</h4>
                            <p>Your selected path is /{{ $parent->par_now == "" ? 'Root' : $in.'/'.$parent->par_now}}</p>
                        </div>
                        <div class="modal-body">
                            <div class="fileUpload btn btn-primary">
                                <span>Choose File</span>
                                <input type="file" id="file" name="file" class="upload" />
                            </div>
                            <input type="hidden" name="dummy_path" value="{{$parent->par_now}}"/>
                            <input type="hidden" name="dummy_store" value="{{$in}}"/>
                            <input type="hidden" name="connection_name" value="{{$in}}"/>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <span id="file-selected" style="padding-left: 5px">No file chosen</span>
                            <h6 id="chstr-text" class="displayNone">

                            </h6>
                            <h6 id="rdd-text" class="displayNone">

                            </h6>
                            <div class="panel panel-primary displayNone" id="panel-priority">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Please select your storage.</h3>
                                </div>
                                <div class="panel-body priority thin-scrollbar" id="priority-storages">
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
            <script>
                document.getElementById("file").onchange = function () {
                    document.getElementById("file-selected").innerHTML = this.value.replace("C:\\fakepath\\", "");
                    document.getElementById('rdd-text').className = 'displayBlock';
                    var rdd_text = document.getElementById('rdd-text');
                    rdd_text.innerHTML = '<div class="loader"> <span></span><span></span><span></span><span></span> </div>Redundancy check from Filename and size...';
                    trig_redundancy('form-upload-dummy');
                    checkStorage('form-upload-dummy');

                };
            </script>
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
                <form id="form-upload" action="{{ url('upload') }}" id="upload_form" target="hiddenIframe" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload</h4>
                            <p>Your selected path is /{{ $parent->par_now == "" ? 'Root' : $in.'/'.$parent->par_now}}</p>
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
                            <h6 id="rdd-text" class="displayNone">
                                <div class="loader">
                                    <span></span><span></span><span></span><span></span>
                                </div>
                                Redundancy checking...
                                {{--<span class="glyphicon glyphicon-ok-sign rdd-success" aria-hidden="true"></span>--}}
                                {{--File doesn't exist in this drive.--}}
                            </h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="button" onclick="uploadSubmit()" class="btn btn-primary" id="btn-upload" value="Upload">
                            {{--onclick="uploadSubmit()"--}}
                        </div>
                    </div>
                </form>
            </div>
            <script>
                document.getElementById("file").onchange = function () {
                    document.getElementById("file-selected").innerHTML = this.value.replace("C:\\fakepath\\", "");
                    //document.getElementById('panel-priority').className = 'panel panel-primary displayBlock';
                    //var priority_box = document.getElementById('priority-storages');
                    //priority_box.innerHTML = '<span class="loading style-1"></span>';
                    //trig_priority();
                    document.getElementById('rdd-text').className = 'displayBlock';
                    var rdd_text = document.getElementById('rdd-text');
                    rdd_text.innerHTML = '<div class="loader"> <span></span><span></span><span></span><span></span> </div>Redundancy checking...';
                    trig_redundancy('form-upload');
                    checkStorage('form-upload');
                };
            </script>
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
                            <tr class="withItemMenu" id="{{ $val['path'] }}" value="{{ $val['path'] }}" data-conname="{{ $val['connection_name'] }}"
                                    data-name="{{ $val['name'] }}">
                                <td class="th-icon-cloud">
                                    <div class="div-circle-icon">
                                        <img src="{{ URL::asset('images/logo-provider/'. $val['provider_logo']) }}">
                                    </div>
                                </td>
                                <td class="th-name">
                                    @if ($val['is_dir'])
                                        <span class="glyphicon glyphicon-folder-close"></span>
                                        <a href="{{ Request::getBaseUrl() . "/home/" .$cname . $val['path'] . ($cname == 'all' ? '?in='.$val['connection_name'] : '')}}">
                                            <span class="dir">{{ $val['name'] }}</span>
                                            <br><span class="text-muted font-12">in</span><span class="text-primary font-12">{{$val['connection_name'] . $val['path_name'] }}</span>
                                        </a>
                                    @else
                                            <span>{{ $val['name'] }}</span>
                                        <br><span class="text-muted font-12">in</span><span class="text-primary font-12">{{$val['connection_name'] . $val['path_name'] }}</span>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Rename-->
<div class="modal fade bs-example-modal-lg" id="modal-rename" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <form id="form-rename" action="#"> //target="hiddenIframe" method="POST" enctype="multipart/form-data"
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Rename</h4>
                </div>
                <div class="modal-body">
                    <input type='text' id='new_name' name='new_name'>
                    <input type="hidden" id="extension" name="extension">
                    <input type="hidden" id="rename_file" name="file">
                    <input type="hidden" id="rename_connection" name="connection_name">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="modal-footer">
                    {{--<button class="btn btn-primary" id="create-copy" value="Upload">--}}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" onclick="renameSubmit()" class="btn btn-primary" id="btn-rename" >Save</button>
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
        <form id="form-transferFile" action="{{ url('transferFile') }}" target="hiddenIframe" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Transfer to another drive.</h4>
                </div>
                <div class="modal-body transfer-modal-body">
                    <div class="transfer-box">
                        <div id="transfer-box" class="btn-group" data-toggle="buttons">
                    </div>
                    </div>
                    <input type="hidden" name="tf_file" id="tf_file">
                    <input type="hidden" name="from_connection" id="from_connection">
                    <input type="hidden" name="mime_type" id="mime_type">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" onclick="transferFileSubmit()" class="btn btn-primary" id="btn-rename" >Transfer</button>
                    {{--<button class="btn btn-primary" id="create-copy" value="Upload">--}}

                </div>
            </div>
        </form>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<iframe name="hiddenIframe" id="hiddenIframe" style="display: none;" ></iframe>
{{--<iframe name="hiddenIframe" id="hiddenIframe" width="70%" height="500px"></iframe>--}}
<script>
    $('#form-rename').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    $('#form-createFolder').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    $('#form-upload').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    $('#form-transfer').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    function renameSubmit(){
        $('#modal-rename').modal('hide');
        var p_loading = document.getElementById('p-loading');
        p_loading.className = 'p-loading';
        $.ajax({
            type: "POST",
            url : $('#ajr').attr('data-rename').trim(),
            data:new FormData($("#form-rename")[0]),
            processData: false,
            contentType: false,
            success : function(data){
                console.log(data);
                location.reload();
            }
        },"json");

    }
    function createFolderSubmit(){
        $('#modal-new-folder').modal('hide');
        var p_loading = document.getElementById('p-loading');
        p_loading.className = 'p-loading';
        $.ajax({
            type: "POST",
            url : $('#ajr').attr('data-createFolder').trim(),
            data:new FormData($("#form-createFolder")[0]),
            processData: false,
            contentType: false,
            success : function(data){
                console.log(data);
                alert('Create Folder Complete.');
                location.reload();
            }
        },"json");

    }
    function uploadSubmit(){
        $('#modal-upload').modal('hide');
        var p_loading = document.getElementById('p-loading');
        p_loading.className = 'p-loading';
        $.ajax({
            type: "POST",
            url : $('#ajr').attr('data-upload').trim(),
            data:new FormData($("#form-upload")[0]),
            processData: false,
            contentType: false,
            success : function(data){
                console.log(data);
                alert('Upload File Complete.');
                location.reload();
            }
        },"json");

    }
    function transferFileSubmit(){
        $('#modal-transfer').modal('hide');
        var p_loading = document.getElementById('p-loading');
        p_loading.className = 'p-loading';
        $.ajax({
            type: "POST",
            url : $('#ajr').attr('data-transferFile').trim(),
            data:new FormData($("#form-transferFile")[0]),
            processData: false,
            contentType: false,
            success : function(data){
                console.log(data);
                alert('Transfer File Complete.');
                location.reload();
            }
        },"json");

    }
</script>
<script src="{{ URL::asset('js/index-board.script.js') }}"></script>
@include("components.contextmenu")
