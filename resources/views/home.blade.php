
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GatherCloud</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/indexUI.css') }}">
</head>
<body>
    <div id="top-bar">
        <div id="logo">&lt;Logo&gt;</div>
        <div id="username"> Username: <b>{{ Auth::user()->name }}</b> <span class="glyphicon glyphicon-cog"></span> <a href="logout">Logout</a></div>

    </div>

    <div id="nav-bar">
        <div id="addCloud"><span class="glyphicon glyphicon-plus"><a href="home/add">+</a></span>Add Cloud Account</div>
        <hr />
        <div id="myCloud">
            <div><span class="glyphicon glyphicon-cloud"></span><a href="#">Box</a></div>
            <div><span class="glyphicon glyphicon-cloud"></span><a href="#">Copy</a></div>
            <div><span class="glyphicon glyphicon-cloud"></span><a href="#">Dropbox</a></div>
            <div><span class="glyphicon glyphicon-cloud"></span><a href="#">Google Drive</a></div>
            <div><span class="glyphicon glyphicon-cloud"></span><a href="#">OneDrive</a></div>
        </div>


    </div>

    <div id="board">

</div>

</body>




</html>