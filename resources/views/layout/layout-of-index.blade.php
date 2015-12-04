<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gathercloud</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/jquery-2.1.4.min.js') }}"></script>
</head>
<body>

<div id="top-bar" class="top-bar">
    <div id="logo" class="logo">&lt;Logo&gt;</div>
    <div id="userMenu" class="userMenu">
        <div class="dropdown">
            <button class="btn-userMenu dropdown-toggle " type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dd-userMenu" aria-labelledby="dropdownMenuDivider">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li class="itemMenu-separator"></li>
                <li><a href="#">Something else here</a></li>
                <li><a href="#">Log out</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="box-lv-1">
    <div id="side-bar" class="side-bar">
        <div id="add-cloud" class="add-cloud"><button class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>
                <a href="{{ url('/add') }}">Add Cloud Account</a></button></div>
        <div class="itemMenu-separator"></div>
        <div id="my-cloud" class="my-cloud">
            <ul id="list-cloud" class="list-cloud">
                <li><div><span class="glyphicon glyphicon-cloud"></span><a href="{{ url('/home') }}">All in one</a> </div></li>
                @foreach ($conn as $c)
                    <li><div><span class="glyphicon glyphicon-cloud"></span>
                            <a href="{{ url("/home/{$c->connection_name}") }}">{{ $c->connection_name }}</a>
                        </div></li>
                @endforeach

                {{--<li><div><span class="glyphicon glyphicon-cloud"></span>{{$conn}}</div></li>--}}
                {{--<li><div><span class="glyphicon glyphicon-cloud"></span>Box</div></li>--}}
                {{--<li><div><span class="glyphicon glyphicon-cloud"></span>Copy</div></li>--}}
                {{--<li><div><span class="glyphicon glyphicon-cloud"></span>Dropbox</div></li>--}}
                {{--<li><div><span class="glyphicon glyphicon-cloud"></span>Google Drive</div></li>--}}
                {{--<li><div><span class="glyphicon glyphicon-cloud"></span>OneDrive</div></li>--}}
            </ul>
        </div>
    </div>

    <div id="board" class="board">
    @yield("content")
    </div>

</div>


@extends("components.contextmenu")
@extends("components.script")
</body>
</html>