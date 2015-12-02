@extends('layout.layout-of-index')

@section('content')
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
@endsection