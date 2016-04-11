@extends('layouts.master-index')

@section('content')
    <div id="board" class="board">
        blahh
    <button onclick="goToSelect()">Continue</button>
    <script>
        document.getElementById('add-gtl').style.backgroundColor = "white";
        function goToSelect(){
            var url = window.location.pathname + "/select";
            // alert(url);
            $("#board").load(url);
        }

    </script>
    </div>

@endsection