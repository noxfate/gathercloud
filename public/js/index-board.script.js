
$("body").css("cursor", "default");
document.getElementById('let-in-folder-progress').className = "";
// set up jQuery with the CSRF token, or else post routes will fail
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

// handlers
function onGetClick(event) {
    // we're not passing any data with the get route, though you can if you want
    var dir = $(this).attr('value');
    var conname = $(this).attr('data-conname');
    if (window.location.pathname.search("search") == -1){
        var url = window.location.pathname + "?path=" + encodeURIComponent(dir)
            + "&connection_name=" + encodeURIComponent(conname);
    }else{
        var path = window.location.pathname.replace('/search','');
        var url = path + "?path=" + encodeURIComponent(dir) + "&connection_name=" + encodeURIComponent(conname);
    }
    //alert(url);
    $("body").css("cursor", "progress");
    document.getElementById('let-in-folder-progress').className = "let-in-folder-progress";
    $("#board").load(url);
}

function onPostClick(event) {
    // we're passing data with the post route, as this is more normal
    $.post('/ajax/post', {payload: 'hello'}, onSuccess);
}

function onSuccess(data, status, xhr) {
    // with our success handler, we're just logging the data...
    console.log(data, status, xhr);

    // but you can do something with it if you like - the JSON is deserialised into an object
    console.log(String(data.value).toUpperCase());

}

function trig_priority(){

    $.ajax({
        type: "POST",
        url : $('#ajr').attr('data-getStorages').trim(),
        success : function(data){
            console.log(data);
            setStoragesBar(data);
            $('.bar-percentage[data-percentage]').each(function () {
                var progress = $(this);
                var percentage = ($(this).attr('data-percentage'));
                $({countNum: 0}).animate({countNum: percentage}, {
                    duration: 2000,
                    easing:'linear',
                    step: function() {
                        // What todo on every count
                        var pct = Math.ceil(this.countNum) + '%';
                        progress.text(pct) && progress.siblings().children().css('width',pct);
                    }
                });
            });
        }
    },"json");

}

function setStoragesBar($data){
    data = JSON.parse($data);
    console.log(data);
    var priority_box = document.getElementById('priority-storages');
    priority_box.innerHTML = "";
    for (var key in data) {
        // skip loop if the property is from prototype
        if (!data.hasOwnProperty(key)) continue;

        var obj = data[key];

        var main_div = document.createElement("div");
        main_div.className = 'radio';

        var lb_radio = document.createElement("label");
        var input_radio = document.createElement("input");
        input_radio.type = "radio";
        input_radio.name = "real_store";
        input_radio.value = obj.connection_name;
        var div_text = document.createElement("div");
        div_text.className = "limit-text";
        div_text.innerHTML = obj.connection_name;
        var div_p = document.createElement("div");
        div_p.className = "div_p";
        var p_text = document.createElement("p");
        p_text.className = "text-muted";
        p_text.innerHTML = obj.remain + " free of " + obj.quota;

        var div_main_bar = document.createElement("div");
        div_main_bar.className = "bar-main-container azure";
        var div_warp = document.createElement("div"); div_warp.className = "wrap";
        var div_bar_per = document.createElement("div"); div_bar_per.className = "bar-percentage";
        div_bar_per.innerHTML = obj.percent + "%";
        div_bar_per.setAttribute("data-percentage",parseFloat(obj.percent));
        var div_bar_con = document.createElement("div"); div_bar_con.className = "bar-container";
        var div_bar = document.createElement("div");
        div_bar.className = "bar";

        lb_radio.appendChild(input_radio);
        lb_radio.appendChild(div_text);
        div_p.appendChild(p_text);
        lb_radio.appendChild(div_p);

        div_bar_con.appendChild(div_bar);
        div_warp.appendChild(div_bar_per);
        div_warp.appendChild(div_bar_con);
        div_main_bar.appendChild(div_warp);

        main_div.appendChild(lb_radio);
        main_div.appendChild(div_main_bar);
        priority_box.appendChild(main_div);
    }
}

function trig_redundancy(){
    $.ajax({
        type: "POST",
        url : $('#ajr').attr('data-redundancyCheck').trim(),
        data:new FormData($("#upload_form")[0]),
        processData: false,
        contentType: false,
        success : function(data){
            console.log(data);
            setRddStatus(data);
        }
    },"json");
}


function setRddStatus($data){
    data = JSON.parse($data);
    var rdd_text = document.getElementById('rdd-text');
    if(!jQuery.isEmptyObject(data)){
        var text = " This file already exists in <i>";
        for (var v in data) {
            text += data[v] + ", ";
            console.log(data[v]);
        }
        text = text.substring(0, text.length -2);
        rdd_text.innerHTML = '<span class="glyphicon glyphicon-exclamation-sign rdd-warning" aria-hidden="true"></span>'
                            + text +"</i>";
    } else {
        rdd_text.innerHTML = '<span class="glyphicon glyphicon-ok-sign rdd-success" aria-hidden="true"></span>'
                                + " File doesn't exist in all drives.";
        console.log("This file doesn't exist in all drives.");
    }

}

function trig_getFolderList(e){
    console.log($(e).attr('data-connection_name'));
    var param = {connection_name:$(e).attr('data-connection_name'),
                path:$(e).attr('data-path')};
    $.ajax({
        type: "POST",
        url : $('#ajr').attr('data-getFolderList').trim(),
        data: param,
        success : function(data){
            setFolderList(e,data);
        }
    },"json");

}

function setFolderList(e,$data){
    data = JSON.parse($data);
    var ul_tf = document.createElement('ul');
    ul_tf.className = 'ul-transfer';
    e.className = 'glyphicon glyphicon-minus-sign gg-margin-right-5 gg-hover';
    e.parentElement.appendChild(ul_tf);
    for (var v in data) {
        console.log(data[v]);
        var li = document.createElement('li');
        li.innerHTML = '<span class="glyphicon glyphicon-plus-sign gg-margin-right-5 gg-hover" data-connection_name="'+data[v]['connection_name']+'" data-path="'+data[v]['path']+'" onclick="trig_getFolderList(this)"></span>' +
            '<label class="btn btn-default">' +
            '<input type="radio" name="to_path" value="' + data[v]['path'] +
            '"><input type="radio" name="to_connection_name" value="' + data[v]['connection_name'] + '">' +
            data[v]['name'] + '</label>';
            //'<a class="btn btn-link">' +
            //data[v]['name'] + '</a>';
        ul_tf.appendChild(li);
    }
}

document.getElementById("file").onchange = function () {
    document.getElementById("file-selected").innerHTML = this.value.replace("C:\\fakepath\\", "");
    document.getElementById('panel-priority').className = 'panel panel-primary displayBlock';
    document.getElementById('rdd-text').className = 'displayBlock';
    var priority_box = document.getElementById('priority-storages');
    priority_box.innerHTML = '<span class="loading style-1"></span>';
    var rdd_text = document.getElementById('rdd-text');
    rdd_text.innerHTML = '<div class="loader"> <span></span><span></span><span></span><span></span> </div>Redundancy checking...';
    trig_priority();
    trig_redundancy();

};

// listeners
// $('button#get').on('click', onGetClick);
//$('span#dir').on('click', onGetClick);
$('button#post').on('click', onPostClick);
