<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gathercloud</title>
    @include('components.main_script')
    {{--<link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap.min.css')}}">--}}
    {{--<link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">--}}
    {{--<link rel="stylesheet" href="{{URL::asset('css/cloud-add.css')}}">--}}
    {{--<script src="{{ URL::asset('js/jquery.min.js') }}"></script>--}}
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
                <li><a href="{{ url('/setting/profile') }}">Profile Setting</a></li>
                <li><a href="{{ url('/setting/cloud') }}">Cloud Connection</a></li>
                <li class="itemMenu-separator"></li>
                <!-- <li><a href="#">Something else here</a></li> -->
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
                <li id="side-bar-select-all">
                    <a href="{{ url('/home') }}">
                        <div>
                            <span class="glyphicon glyphicon-cloud"></span>
                            All in one
                        </div>
                    </a>
                </li>
                @foreach ($conn as $c)
                    <li id="side-bar-select-{{ $c->connection_name }}">
                        <a href="{{ url("/home/{$c->connection_name}") }}">
                            <div><span class="glyphicon glyphicon-cloud"></span>
                                {{ $c->connection_name }}
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="itemMenu-separator"></div>
        <div id="add-gtl" class="add-cloud">
            <a href="{{ url('/gtl') }}">
                <button class="btn btn-primary" >
                    {{--id="gtl-btn"--}}
                    <span class="glyphicon glyphicon-plus"></span>
                    Create new GatherLink
                </button>
            </a>
        </div>

        <div class="itemMenu-separator"></div>
        <div id="my-cloud" class="my-cloud">
            <ul id="list-cloud" class="list-cloud">
                @foreach ($link as $key => $val)
                    <li id="side-bar-select-{{ $key }}">
                        <a href="{{ url("/gtl/{$key}") }}">
                            <div><span class="glyphicon glyphicon-cloud"></span>
                                {{ $key }}
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>


    @yield("content")

</div>
</body>
</html>