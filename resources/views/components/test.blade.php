<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gathercloud</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/cloud-index.css')}}">

</head>
<body>

<div id="top-bar" class="top-bar">
    <div id="logo" class="logo">&lt;Logo&gt;</div>
    <div id="userMenu" class="userMenu">
        <div class="dropdown">
            <button class="btn-userMenu dropdown-toggle " type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dd-userMenu" aria-labelledby="dropdownMenuDivider">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li class="itemMenu-separator"></li>
                <li><a href="#">Something else here</a></li>
                <li><a href="#">Log out</a></li>
            </ul>
        </div>
    </div>
</div>


<script type="text/javascript" src="{{URL::asset('js/jquery-2.1.4.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/bootstrap.js')}}"></script>

@extends("components.contextmenu")
</body>
</html>