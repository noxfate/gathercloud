@extends('layouts.master-index')

@section('content')
    <div id="board" class="board">
        blahh
    <button onclick="goToSelect()">Continue</button>
    <script>
//        document.getElementById('add-gtl').className = "btn-add add-withSelect"
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