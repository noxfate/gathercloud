<input type="hidden" id="ajr" data-selectIn="{{URL::route("selectIn")}}" data-select="{{URL::route("select")}}">
<script>
    document.getElementById('side-bar-select-{{ $cname }}').className = "withSelect";
</script>
<div id="box-st-bar" class="box-st-bar">
    <div class="create-bar">
        <div class="form-group">
            <label for="gtl-name" class="col-lg-4 control-label" style="padding: 2px 0px 0px 0px;">
                <h5 style="color: #666;margin: 0px;"><span class="label label-default" style="color: black">GTL Name</span></h5>
            </label>
            <div class="col-lg-7" style="padding:0px">
                <input type="text" class="form-control input-sm" id="gtl-name" style="font-size: 18px" placeholder="name" required>
            </div>
        </div>
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
    <div class="panel panel-default panel-gtl">
        <div class="panel-heading">
            <div id="nav-bar" class="nav-bar">
                @if (!empty($parent))
                    <ul class="breadcrumb">
                        @for ($i = 0; $i < count($parent->par_name); $i++)
                            <li>
                                @if ($i == 0)
                                    <a onclick="select()">{{ $parent->par_name[$i] }}</a>
                                @else
                                    <a onclick="selectIn($(this))" data-conname="{{$in}}"
                                          data-value="{{urldecode($parent->par_path[$i])}}">{{ $parent->par_name[$i] }}</a>
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
                    <th class="th-icon-cb-gtl"><span class="glyphicon glyphicon-check"></span></th>
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
                                <td class="th-icon-cloud"><input class="gtl-chkbox" data-conname="{{ $val['connection_name']}}" data-path="{{urldecode($val['path'])}}"
                                                                 data-value="{{$val['connection_name'] . $val['path']}}" data-name="{{$val['name']}}"
                                                                 data-size="{{ $val['size'] }}" data-date="{{ $val['modified'] }}"
                                                                 data-icon="{{$val['provider_logo']}}"
                                                                 data-path-name="{{$val['path_name']}}"
                                                                 type="checkbox"></td>
                                <td class="th-icon-cloud">
                                    <div class="div-circle-icon">
                                        <img src="{{ URL::asset('images/logo-provider/'. $val['provider_logo']) }}">
                                    </div>
                                </td>
                                <td class="th-name">
                                    @if ($val['is_dir'])
                                        <span class="glyphicon glyphicon-folder-close"></span>
                                        <span class="dir" data-conname="{{ $val['connection_name'] }}" data-value="{{ urldecode($val['path']) }}" onclick="selectIn($(this))">{{ $val['name'] }}</span>
                                        <br><span class="text-muted font-12">in</span><span class="text-primary font-12">{{$val['connection_name']. $val['path_name'] }}</span>
                                    @else
                                        <span class="glyphicon glyphicon glyphicon-file"></span><span>{{ $val['name'] }}</span>
                                        <br><span class="text-muted font-12">in</span><span class="text-primary font-12">{{$val['connection_name']. $val['path_name'] }}</span>
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
    <div class="gtl-buttom-bar">
        <h5 class="text-primary" style="display: inline;padding-right: 3px">Yours Selected items <span id="gtl-label-count" class="badge">0</span></h5>
        <button id="gtl-btn-continue" class="btn btn-primary btn-lg">Continue</button>
    </div>
</div>

{{--// becuz window load on board--}}
<link rel="stylesheet" href="{{URL::asset('css/bootswatch.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">
{{--<script src="{{ URL::asset('js/index-board.script.js') }}"></script>--}}
<script src="{{ URL::asset('js/gtl-board.script.js') }}"></script>
