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
    <div id="logo" class="logo"><img src="{{URL::asset('images/logo-mini-white-gtc.png')}}"></div>
    <div style="position: absolute;right: 50px;top: 17px;">{{ $usr->first_name." ".$usr->last_name  }}</div>
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
                        @foreach ($conn as $index => $c)
                            <li id="side-bar-select-{{ $c->connection_name }}">
                                <a class="btn btn-link"  href="{{ url("/home/{$c->connection_name}") }}">
                                    <div class="div-circle-icon">
                                        <img src="{{ URL::asset('images/logo-provider/'. $logo[$index]->provider_logo) }}">
                                    </div>
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
                                <a class="btn btn-link" href="{{ url("/gtl/{$key}") }}">
                                    <div><span class="glyphicon glyphicon-th-list"></span>
                                        {{ $key }}
                                    </div>
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
    </div>


    @yield("content")

</div>
</body>
</html>