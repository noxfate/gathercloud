@extends('layouts.master-index')

@section('content')
    <div id="board" class="board">
        <div class="gtl-box" style="background-color: #f9f9f9;">
            <div class="gtl-landing">
                <img src="{{ URL::asset('images/gtl-concept.png') }}" width="100%">
                <h1><span class="glyphicon glyphicon-list"></span>  GatherLink</h1>
                <p>GatherLinks is a simple feature from GatherCloud allows you to compose a simple view to share to others.</p>
                <button class="btn btn-primary btn-lg" onclick="goToSelect()">Continue</button>
            </div>
        </div>
        <script>
            var url = window.location.pathname + "/select/all";
            // alert(url);
            $("#board").load(url);

            function goToSelect(){
                var url = window.location.pathname + "/select/all";
                // alert(url);
                $("#board").load(url);
            }

            document.getElementById('tab-drives').className = "";
            document.getElementById('tab-gtls').className = "active";
            document.getElementById('content-drives').className = "tab-pane fade";
            document.getElementById('content-gtls').className = "tab-pane fade active in";
        </script>
    </div>
@endsection