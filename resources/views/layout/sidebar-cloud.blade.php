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