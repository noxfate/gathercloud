@extends('layouts.master-another-seen')
@section('content')
    <div class="gtl-box">
            <h4><span class="label label-default" style="color: black">GTL Name</span>&nbsp;<span>{{ $lname }}</span></h4>
        <h6>By <span class="text-primary">
                {{ $usr->first_name." ".$usr->last_name  }}
            </span></h6>
            <hr width="50%">
            <div style="margin-bottom: 10px">
                <div style="display: inline-block;">
                    <h6 class="text-primary" style="display: inline;padding-right: 3px">Items <span class="badge">{{$data->count()}}</span></h6>
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
                            @foreach($data as $index =>  $d)
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
                                    </td>
                                    <td class="th-size">{{ $d->size }}</td>
                                    <td class="th-last-mo">{{ $d->modified }}</td>
                                    <td class="th-action"><span class="caret action"></span></td>
                                </tr>
                            @endforeach
                        @endif
                        {{--@for($i=0 ; $i<3 ; $i++)--}}
                            {{--<tr class="withItemMenu">--}}
                                {{--<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>--}}
                                {{--<td class="th-name">--}}
                                    {{--<span class="glyphicon glyphicon glyphicon-file"></span>--}}
                                    {{--<a href="#" alt="textttttt" title="baba">--}}
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
                <input type="hidden" name="items" id="post-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
            <br>
    </div>
@endsection