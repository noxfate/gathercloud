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

<div class="box-lv-1">
    <div id="side-bar" class="side-bar">
        <div id="add-cloud" class="add-cloud"><button class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>Add Cloud Account</button></div>
        <div class="itemMenu-separator"></div>
        <div id="my-cloud" class="my-cloud">
            <ul id="list-cloud" class="list-cloud">
                <li class="withSelect"><div><span class="glyphicon glyphicon-cloud"></span>All in one</div></li>
                <li><div><span class="glyphicon glyphicon-cloud"></span>Box</div></li>
                <li><div><span class="glyphicon glyphicon-cloud"></span>Copy</div></li>
                <li><div><span class="glyphicon glyphicon-cloud"></span>Dropbox</div></li>
                <li><div><span class="glyphicon glyphicon-cloud"></span>Google Drive</div></li>
                <li><div><span class="glyphicon glyphicon-cloud"></span>OneDrive</div></li>
            </ul>
        </div>
    </div>

    <div id="board" class="board">
        <div id="box-nav-bar" class="box-nav-bar">
            <div id="nav-bar" class="nav-bar">
                <a href="#"><span class="glyphicon glyphicon-cloud"></span></a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="#"><span class="glyphicon glyphicon-folder-open"></span>Test</a>
            </div>
            <div id="create-bar" class="create-bar">
                <button id="new-folder" class="btn btn-default"><div class="icon-new-folder"></div> New Folder</button>
                <button id="file-upload" class="btn btn-default"><span class="glyphicon glyphicon-cloud-upload"></span> File Upload</button>
            </div>
        </div>

        <table id="table-header" class="table-header">
            <tr>
                <th class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></th>
                <th class="th-name">Name</th>
                <th class="th-size">Size</th>
                <th class="th-last-mo">Last modified</th>
                <th class="th-action"></th>
            </tr>
        </table>
        <div id="board-body" class="board-body">
        <table class="table-body table-hover table-striped" >
            <tr class="withItemMenu">
                <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
                <td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>
                <td class="th-size">10 KB</td>
                <td class="th-last-mo">2015-12-12 15:15</td>
                <td class="th-action"><span class="caret action"></span></td>
            </tr>
            <tr class="withItemMenu">
                <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
                <td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>
                <td class="th-size">10 KB</td>
                <td class="th-last-mo">2015-12-12 15:15</td>
                <td class="th-action"><span class="caret action"></span></td>
            </tr>
            <tr class="withItemMenu">
                <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
                <td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>
                <td class="th-size">10 KB</td>
                <td class="th-last-mo">2015-12-12 15:15</td>
                <td class="th-action"><span class="caret action"></span></td>
            </tr>
            <tr class="withItemMenu">
                <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
                <td class="th-name"><span class="glyphicon glyphicon-folder-close   "></span><a href="#"> test <te></te>st test</a></td>
                <td class="th-size">10 KB</td>
                <td class="th-last-mo">2015-12-12 15:15</td>
                <td class="th-action"><span class="caret action"></span></td>
            </tr>

        </table>
        </div>
    </div>

</div>


@extends("components.contextmenu")
@extends("components.script")
</body>
</html>