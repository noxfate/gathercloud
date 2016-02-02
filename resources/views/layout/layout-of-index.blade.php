<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gathercloud</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-add.css')}}">
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/jquery.contextmenu.js') }}"></script>
</head>
<body>

<div id="top-bar" class="top-bar">
    <div id="logo" class="logo">&lt;Logo&gt;</div>
    <div id="userMenu" class="userMenu">
        <div class="dropdown">
            <button class="btn-userMenu dropdown-toggle " type="button" id="dropdownMenu1" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dd-userMenu" aria-labelledby="dropdownMenuDivider">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li class="itemMenu-separator"></li>
                <li><a href="#">Something else here</a></li>
                <li><a href="{{url('/logout')}}">Log out</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="box-lv-1">
    <div id="side-bar" class="side-bar">
        <div id="add-cloud" class="add-cloud">
            <a href="{{ url('/add') }}">
                <button class="btn btn-primary">
                    <span class="glyphicon glyphicon-plus"></span>
                    Add Cloud Account
                </button>
            </a>
        </div>
        <div class="itemMenu-separator"></div>
        <div id="my-cloud" class="my-cloud">
            <ul id="list-cloud" class="list-cloud">
                <li>
                    <a href="{{ url('/home') }}">
                        <div>
                            <span class="glyphicon glyphicon-cloud"></span>
                            All in one
                        </div>
                    </a>
                </li>
                @foreach ($conn as $c)
                    <li id="side-bar-select-{{ $c->id }}">
                        <a href="{{ url("/home/{$c->connection_name}") }}">
                            <div><span class="glyphicon glyphicon-cloud"></span>
                                {{ $c->connection_name }}
                            </div>
                        </a>
                    </li>
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


    @yield("content")

</div>
<script>

</script>

@extends("components.contextmenu")
@extends("components.script")
</body>
</html>