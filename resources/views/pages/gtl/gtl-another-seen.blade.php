@extends('layouts.master-another-seen')
@section('content')
    <div class="gtl-box">
            <h4><span class="label label-default" style="color: black">GTL Name</span>&nbsp;<span>test gtl</span></h4>
        <h6>By <span class="text-primary">admin gtc</span></h6>
            <hr width="50%">
            <div style="margin-bottom: 10px">
                <div style="display: inline-block;">
                    <h6 class="text-primary" style="display: inline;padding-right: 3px">Items <span class="badge">3</span></h6>
                </div>
                <div style="display: inline-block;border-left: 1px solid #eee;padding-left: 15px;margin-left: 10px">
                    <button type="button" class="btn btn-default btn-sm" aria-hidden="true">
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
                        @for($i=0 ; $i<3 ; $i++)
                            <tr class="withItemMenu">
                                <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
                                <td class="th-name">
                                    <span class="glyphicon glyphicon glyphicon-file"></span>
                                    <a href="#" alt="textttttt" title="baba">
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
    </div>
@endsection