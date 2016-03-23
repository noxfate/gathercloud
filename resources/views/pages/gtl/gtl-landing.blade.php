@extends('layouts.master-index')

@section('content')
    <div id="board" class="board">
blah balh
<button  onclick="goToSelect()">Continue</button>
    </div>

    <script>
        function goToSelect() {
            var url = window.location.pathname.replace('/gtl','/home/select');
            $("body").css("cursor", "progress");
            $("#board").load(url);
        }
    </script>
@endsection