<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gathercloud</title>
    @include('components.main_script')
</head>
<body>

<div id="top-bar" class="top-bar">
    <div id="logo" class="logo"><img src="{{URL::asset('images/logo-mini-white-gtc.png')}}" height="40px"></div>
    <div style="position: absolute;right: 50px;top: 17px;">admin gtc</div>
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
        <ul class="nav nav-tabs">
            <li id="tab-drives" class="active"><a href="#content-drives" data-toggle="tab" aria-expanded="true">Drives</a></li>
            <li id="tab-gtls" class=""><a href="#content-gtls" data-toggle="tab" aria-expanded="false">GatherLinks</a></li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="content-drives">
                <div id="add-cloud" class="btn-add">
                    <a href="{{ url('/add') }}">
                        <button class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            Add New Drive
                        </button>
                    </a>
                </div>
                <div id="my-cloud" class="my-cloud">
                    <ul id="list-cloud" class="list-cloud">
                        <li id="side-bar-select-all">
                            <a class="btn btn-link" href="{{ url('/home/all') }}">
                                    <span class="glyphicon glyphicon-cloud"></span>
                                    All in one
                            </a>
                        </li>
                        @foreach ($conn as $c)
                            <li id="side-bar-select-{{ $c->connection_name }}">
                                <a class="btn btn-link"  href="{{ url("/home/{$c->connection_name}") }}">
                                    <span class="glyphicon glyphicon-cloud"></span>
                                        {{ $c->connection_name }}


                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="tab-pane fade" id="content-gtls">
                <div id="add-gtl" class="btn-add">
                    <a href="{{ url('/gtl') }}">
                        <button class="btn btn-primary" >
                            {{--id="gtl-btn"--}}
                            <span class="glyphicon glyphicon-plus"></span>
                            Create new GatherLink
                        </button>
                    </a>
                </div>
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
                            {{--<li id="side-bar-select-test">--}}
                                {{--<a class="btn btn-link" href="#">--}}
                                    {{--<div><span class="glyphicon glyphicon-list"></span>--}}
                                        {{--test gtl--}}
                                    {{--</div>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                    </ul>
                </div>
            </div>
        </div>
    </div>


    @yield("content")

</div>
</body>
</html>