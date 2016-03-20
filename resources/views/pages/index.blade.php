@extends('layout.layout-of-index')

@section('content')
    <div id="board" class="board">
        <div id="box-nav-bar" class="box-nav-bar">
            <div id="nav-bar" class="nav-bar">
                @if (!empty($parent))
                    @for ($i = 0; $i < count($parent->pname); $i++)
                        @if ($i == 0)
                            <a href="{{ url("/home{$parent->ppath[$i]}") }}"><span>{{ $parent->pname[$i] }}</span></a>
                        @else
                            <span class="glyphicon glyphicon-menu-right"></span>
                            <span class="glyphicon glyphicon-folder-open"></span>
                            <span id="dir" class="dir" value="{{$parent->ppath[$i]}}">{{ $parent->pname[$i] }}</span>
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

                <a href="{{ url('upload') }}" id="file-upload" class="btn btn-default"><span class="glyphicon glyphicon-cloud-upload"></span>
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
                <th class="th-icon-cloud"></th>
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
                        document.getElementById('side-bar-select-{{$cmail}}').className = "withSelect";
                    </script>
                    @foreach($data as $d => $val)
                        <tr class="withItemMenu" value="{{ $val['path'] }}">
                            <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>

                            <td class="th-icon-cloud"><input class="gtl-chkbox" id="{{ $d }}" type="checkbox"></td>

                            <td class="th-name">
                            @if ($val['is_dir']  or ($val['size'] == 0))
                                <span class="glyphicon glyphicon-folder-close"></span>
                                {{--<a id="dir" href="{{ Request::url()."/".$val['name'] }}">{{ $val['name'] }}</a></td>--}}
                                <span id="dir" class="dir" alt="{{ $val['provider'] }}" value="{{ $val['path'] }}">{{ $val['name'] }}</span></td>
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
            <button id="gtl-btn-save">Save</button>  <button id="gtl-btn-cancel">Cancel</button>
        </div>
    </div>

    

    {{--<tr class="withItemMenu">--}}
    {{--<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>--}}
    {{--<td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>--}}
    {{--<td class="th-size">10 KB</td>--}}
    {{--<td class="th-last-mo">2015-12-12 15:15</td>--}}
    {{--<td class="th-action"><span class="caret action"></span></td>--}}
    {{--</tr>--}}
    {{--<tr class="withItemMenu">--}}
    {{--<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>--}}
    {{--<td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>--}}
    {{--<td class="th-size">10 KB</td>--}}
    {{--<td class="th-last-mo">2015-12-12 15:15</td>--}}
    {{--<td class="th-action"><span class="caret action"></span></td>--}}
    {{--</tr>--}}
    {{--<tr class="withItemMenu">--}}
    {{--<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>--}}
    {{--<td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>--}}
    {{--<td class="th-size">10 KB</td>--}}
    {{--<td class="th-last-mo">2015-12-12 15:15</td>--}}
    {{--<td class="th-action"><span class="caret action"></span></td>--}}
    {{--</tr>--}}
    {{--<tr class="withItemMenu">--}}
    {{--<td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>--}}
    {{--<td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>--}}
    {{--<td class="th-size">10 KB</td>--}}
    {{--<td class="th-last-mo">2015-12-12 15:15</td>--}}
    {{--<td class="th-action"><span class="caret action"></span></td>--}}
    {{--</tr>--}}


    <script>
        $("body").css("cursor", "default");
        // set up jQuery with the CSRF token, or else post routes will fail
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});


        $(".gtl-chkbox").hide();
        $("#gtl-btn-cancel").hide();
        $("#gtl-btn-save").hide();
        $("#gtl-btn").click(function(){
            $("#gtl-btn-cancel").show();
            $("#gtl-btn-save").show();
            $(".gtl-chkbox").show();
        });
        $(".gtl-chkbox").click(function(){
            if (typeof Storage !== "undefined") { // Support Web Storage
                if (sessionStorage.getItem("selected") === null){
                    sessionStorage.setItem("selected",JSON.stringify([$(this).attr("id")]));
                    // alert("Created");
                }else{
                    var ids = JSON.parse(sessionStorage.getItem("selected"));
                    var index = ids.indexOf($(this).attr("id"));
                    if (index !== -1){
                        ids.splice(index,1);
                    }else{
                        ids.push($(this).attr("id"));
                    }
                    ids.sort();
                    sessionStorage.setItem("selected",JSON.stringify(ids));
                    // alert(ids);
                }
            }
        });
        $("#gtl-btn-save").click(function(e){
            var selected_ids = JSON.parse(sessionStorage.getItem("selected"));
            if (selected_ids === null){
                alert("Please Selected at least 1 item");
            }else{
                window.location.href = window.location.pathname.replace('/home','/gtl/create');;
            }
        });
        $("#gtl-btn-cancel").click(function(e){
            $(".gtl-chkbox").attr('checked',false);
            sessionStorage.removeItem("selected");
            $(".gtl-chkbox").hide();
            $("#gtl-btn-cancel").hide();
            $("#gtl-btn-save").hide();
        });


        // handlers
        function onGetClick(event) {
            // we're not passing any data with the get route, though you can if you want
            var dir = $(this).attr('value');
            var prov = $(this).attr('alt');
            if (window.location.pathname.search("search") == -1){
                var url = window.location.pathname + "?path=" + encodeURIComponent(dir)
                        + "&provider=" + encodeURIComponent(prov);
            }else{
                var path = window.location.pathname.replace('/search','');
                var url = path + "?path=" + encodeURIComponent(dir) + "&provider=" + encodeURIComponent(prov);
            }
            $("body").css("cursor", "progress");
            $("#board").load(url);
        }

        function onPostClick(event) {
            // we're passing data with the post route, as this is more normal
            $.post('/ajax/post', {payload: 'hello'}, onSuccess);
        }

        function onSuccess(data, status, xhr) {
            // with our success handler, we're just logging the data...
            console.log(data, status, xhr);

            // but you can do something with it if you like - the JSON is deserialised into an object
            console.log(String(data.value).toUpperCase());
            ;

        }

        // listeners
        //                $('button#get').on('click', onGetClick);
        $('span#dir').on('click', onGetClick);
        $('button#post').on('click', onPostClick);

    </script>
@endsection