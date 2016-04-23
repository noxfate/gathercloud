@extends('layouts.master-index')

@section('content')
    <div id="board" class="board">
        <div class="gtl-jumbotron">
            <h1>GatherLink</h1>
            <p>This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
            <button class="btn btn-primary btn-lg" onclick="goToSelect()">Continue</button>
        </div>
    <script>
        function goToSelect(){
            var url = window.location.pathname + "/select";
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