<div id="box-nav-bar" class="box-nav-bar">
    <div id="nav-bar" class="nav-bar">
        @if (!empty($parent))
            @for ($i = 0; $i < count($parent->pname); $i++)
                @if ($i == 0)
                    <a href="{{ url("/home{$parent->ppath[$i]}") }}"><span>{{ $parent->pname[$i] }}</span></a>
                @else
                    <span class="glyphicon glyphicon-menu-right"></span>
                    <span class="glyphicon glyphicon-folder-open"></span>
                    <span id="dir" class="dir" alt="{{ $parent->pprovider }}" value="{{$parent->ppath[$i]}}">{{ $parent->pname[$i] }}</span>
                @endif
            @endfor
        @endif
    </div>
    <div id="create-bar" class="create-bar">
        <button id="new-folder" class="btn btn-default">
            <div class="icon-new-folder"></div>
            New Folder
        </button>
        <!--  <button id="file-upload" class="btn btn-default"><span class="glyphicon glyphicon-cloud-upload"></span>
             File Upload
         </bitton> -->

        <a href="{{ url('upload') }}" id="file-upload" class="btn btn-default"><span
                    class="glyphicon glyphicon-cloud-upload"></span>
            File Upload
        </a>

        <form action=" {{ url("/home/search") }}">
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
        {{--<th class="th-icon-cloud"></th>--}}
        <th class="th-name">Name</th>
        <th class="th-size">Size</th>
        <th class="th-last-mo">Last modified</th>
        <th class="th-action"></th>
    </tr>
</table>
<div id="board-body" class="board-body">
    <table class="table-body table-hover table-striped">
        @if (!empty($data))
            <script>
                document.getElementById('side-bar-select-{{$cname}}').className = "withSelect";
            </script>
            @foreach($data as $d => $val)
                <tr class="withItemMenu" value="{{ $val['path'] }}">
                    <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>

                    {{--<td class="th-icon-cloud"><input class="gtl-chkbox" id="{{ $d }}" type="checkbox"></td>--}}

                    <td class="th-name">
                        @if ($val['is_dir']  or ($val['size'] == 0))
                            <span class="glyphicon glyphicon-folder-close"></span>
                            {{--<a id="dir" href="{{ Request::url()."/".$val['name'] }}">{{ $val['name'] }}</a></td>--}}
                            <span id="dir" class="dir" alt="{{ $val['token_id'] }}"
                                  value="{{ $val['path'] }}">{{ $val['name'] }}</span></td>
                    @else
                        <a href="#">{{ $val['name'] }}</a></td>
                    @endif
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


<script src="{{ URL::asset('js/index-board.script.js') }}"></script>
@include("components.contextmenu")
