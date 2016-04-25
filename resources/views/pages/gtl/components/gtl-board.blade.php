<iframe name="hiddenIframe" id="hiddenIframe" style="display: none;" ></iframe>
{{--<iframe name="hiddenIframe" id="hiddenIframe"  ></iframe>--}}
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
                <input type="text" class="form-control input-sm" id="gtl-name" style="font-size: 18px" placeholder="name">
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
                    <th class="th-icon-cb-gtl"><span class="glyphicon glyphicon-check"></span></th>
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
                    @for($i=0 ; $i<6 ; $i++)
                        <tr class="withItemMenu">
                            <td class="th-icon-cloud"><input type="checkbox"></td>
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
    <div class="gtl-buttom-bar">
        <h5 class="text-primary" style="display: inline;padding-right: 3px">Yours Selected items <span class="badge">3</span></h5>
        <button class="btn btn-primary btn-lg">Continue</button>
    </div>
</div>

<script src="{{ URL::asset('js/index-board.script.js') }}"></script>
<script src="{{ URL::asset('js/gtl-board.script.js') }}"></script>
