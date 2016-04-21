<div class="contextMenu" id="myMenu">

    <ul class="itemMenu show-itemMenu">
        <li class="itemMenu-item">
            <button type="button" class="itemMenu-btn">
                <span class="glyphicon glyphicon-folder-open"></span>
                <span class="itemMenu-text">Open</span>
            </button>
        </li>
        <li class="itemMenu-separator"></li>
        <li class="itemMenu-item" id="right-download">
            <button type="button" class="itemMenu-btn">
                <span class="glyphicon glyphicon-download-alt"></span>
                <span class="itemMenu-text">Download</span>
            </button>
        </li>
        <li class="itemMenu-item">
            <button type="button" class="itemMenu-btn">
                <span class="glyphicon glyphicon-share-alt"></span>
                <span class="itemMenu-text">Share</span>
            </button>
        </li>
        <li class="itemMenu-separator"></li>
        <li class="itemMenu-item" id="right-delete">
            <button type="button" class="itemMenu-btn">
                <span class="glyphicon glyphicon-trash"></span>
                <span class="itemMenu-text">Delete</span>
            </button>
        </li>
        <li class="itemMenu-item" id="right-rename">
            <button type="button" class="itemMenu-btn">
                <span class="glyphicon glyphicon-edit"></span>
                <span class="itemMenu-text">Rename</span>
            </button>

        </li>
        <li class="itemMenu-item">
            <button type="button" class="itemMenu-btn">
                <span class="glyphicon glyphicon-move"></span>
                <span class="itemMenu-text">Move</span>
            </button>
        </li>
        <li class="itemMenu-item">
            <button type="button" class="itemMenu-btn">
                <span class="glyphicon glyphicon-copy"></span>
                <span class="itemMenu-text">Copy</span>
            </button>
        </li>
    </ul>



    <style>
        /* itemMenu */

        .itemMenu {
            display: inherit;
            position: absolute;
            width: 150px;
            padding: 5px 0px 5px 0px;
            margin: 0;
            background: #333333;
            z-index: 100;
            border-radius: 5px;
            box-shadow: 1px 1px 4px rgba(0,0,0,.2);
            opacity: 0;
            -webkit-transform: translate(0, 15px) scale(.95);
            transform: translate(0, 15px) scale(.95);
            transition: transform 0.1s ease-out, opacity 0.1s ease-out;
            pointer-events: none;
        }

        .itemMenu-item {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            white-space: nowrap;
        }

        .itemMenu-btn {
            display: block;
            color: #989898;
            font-size: 13px;
            border: 0px;
            cursor: pointer;
            white-space: nowrap;
            padding: 6px 8px;
        }

        button.itemMenu-btn {
            background: none;
            line-height: normal;
            overflow: visible;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            width: 100%;
            text-align: left;
        }

        a.itemMenu-btn {
            outline: 0 none;
            text-decoration: none;
        }

        .itemMenu-text {
            margin-left: 10px;
        }

        .itemMenu-btn .fa {
            position: absolute;
            left: 8px;
            top: 50%;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        .itemMenu-item:hover > .itemMenu-btn {
            color: #000;
            outline: none;
            background-color: whitesmoke;
            /*background: -webkit-linear-gradient(to bottom, #5D6D79, #2E3940);*/
            /*background: linear-gradient(to bottom, #5D6D79, #2E3940);*/
            /*border: 1px solid #2E3940;*/
        }

        .itemMenu-item.disabled {
            opacity: .5;
            pointer-events: none;
        }

        .itemMenu-item.disabled .itemMenu-btn {
            cursor: default;
        }

        .itemMenu-separator {
            display:block;
            margin: 7px 5px;
            height:1px;
            background-color: #999999;
        }

        .itemMenu-item.subItemMenu::after {
            content: "";
            position: absolute;
            right: 6px;
            top: 50%;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-left-color: #808080;
        }

        .itemMenu-item.subItemMenu:hover::after {
            border-left-color: #fff;
        }

        .itemMenu .itemMenu {
            top: 4px;
            left: 99%;
        }

        .show-itemMenu,
        .itemMenu-item:hover > .itemMenu {
            opacity: 1;
            -webkit-transform: translate(0, 0) scale(1);
            transform: translate(0, 0) scale(1);
            pointer-events: auto;
        }

        .itemMenu-item:hover > .itemMenu {
            -webkit-transition-delay: 300ms;
            transition-delay: 300ms;
        }
    </style>

</div>
<script src="{{ URL::asset('js/jquery.contextmenu.js') }}"></script>
<script>
    $('#board-body tbody tr').contextMenu('myMenu', {
        bindings: {
            'right-download': function(t) {
                ShowAction(t, "Download");
            },
            'right-delete': function(t) {
                ShowAction(t, "Delete");
            },
            'right-rename': function(t) {
                ShowAction(t, "Rename");
            }

        }
    });

    function ShowAction(t, a) {
        if(a == "Download"){
            var file = $(t).attr('value');
            var connection_name = $(t).find("td:eq(1)").find("a").find("span").attr('data-conname');
            var indOf = window.location.pathname.indexOf("/home",1);
            var myStr = window.location.pathname.substr(0,indOf );
            var url = myStr + "/download"
            window.open(
                    url + '?connection_name=' + connection_name + '&file=' + file,
                    '_blank' // <- This is what makes it open in a new window.
            );

        } else if(a == "Delete"){
            var file = $(t).attr('value');
            var connection_name = $(t).find("td:eq(1)").find("a").find("span").attr('data-conname');
            var indOf = window.location.pathname.indexOf("/home",1);
            var myStr = window.location.pathname.substr(0,indOf );
            var url = myStr + "/delete"
            alert(file);
            $.ajax({
                type: 'POST',
                url: 'delete',
                data: {
                    file: file,
                    connection_name: connection_name
                },
                success: function(result){
                    alert(result);
                }
            });

        } else if(a == "Rename"){
            var file = $(t).attr('value');
            var connection_name = $(t).find("td:eq(1)").find("a").find("span").attr('data-conname');
            var old_name = $(t).find("td:eq(1)").find("a").find("span").html();
            $("#modal-rename").modal();
            $("#new_name").val(old_name);
            $("#rename_file").val(file);
            $("#rename_connection").val(connection_name);
////            alert(file);
////            alert(connection_name);
////            alert($(t).find("td:eq(1)").find("span").html());
//            var old_name = $(t).find("td:eq(1)").find("span").html();
//            var span = $(t).find("td:eq(1)").html();
//            $(t).find("td:eq(1)").html("<input type='text' id='new_name' name='new_name' value='"+ old_name +"'>" +
//                    "<button id=\"rename_save\">Save</button><button id=\"rename_cancel\">Cancel</button>");
//            $('#rename_cancel').on('click', test);

        }

    }



    function renamePost(file,new_name,connection_name){
        $.ajax({
            type: 'POST',
            url: 'rename',
            data: {
                file: file,
                new_name:new_name,
                connection_name: connection_name
            },
            success: function(result){
                alert(result);
            }
        });
    }
</script>


