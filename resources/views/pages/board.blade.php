<table class="table-body table-hover table-striped" >
    <button id="get">get test</button>
    <button id="post">post test</button>
    @if (!empty($data))
        @foreach($data as $d => $val)
            <tr class="withItemMenu">
                <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
                <td class="th-name">
                    @if ($val['is_dir'])
                        <span class="glyphicon glyphicon-folder-close"></span>
                        {{--<a id="dir" href="{{ Request::url()."/".$val['name'] }}">{{ $val['name'] }}</a></td>--}}
                        <span id="dir">{{ $val['name'] }}</span></td>
                @else
                    <a href="#">{{ $val['name'] }}</a></td>
                @endif
                <td class="th-size">{{ $val['size'] }}</td>
                <td class="th-last-mo">{{ $val['modified'] }}</td>
                <td class="th-action"><span class="caret action"></span></td>
            </tr>
        @endforeach
    @endif
</table>

