$("body").css("cursor", "default");
// set up jQuery with the CSRF token, or else post routes will fail
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});



function selectIn(folder){
    var conname = folder.attr('data-conname');
    var path = folder.attr('data-value');
    console.log(conname);
    console.log(path);
    var url = $('#ajr').attr('data-selectIn').trim()
    console.log(url);
    url = url + "?path=" + encodeURI(path) + "&connection_name=" + encodeURI(conname);
    console.log(url);
    $("#board").load(url);
}

function select(){
    var url = $('#ajr').attr('data-select').trim()
    console.log(url);
    $("#board").load(url);
}

$(".gtl-chkbox").ready(function(){
    if (sessionStorage.getItem("selected") !== null){
        var items = JSON.parse(sessionStorage.getItem("selected"));
        document.getElementById("gtl-label-count").textContent = items.length;
        for (item in items){
            $("input[data-value='"+items[item]+"']").prop("checked",true);
        }
    }

});

$("#gtl-label-count").click(function(){
    if (sessionStorage.getItem("selected") !== null){
        console.log('have session');
        var items = JSON.parse(sessionStorage.getItem("selected"));
        for (item in items){
            $("input[data-value='"+items[item]+"']").prop("checked",false);
        }
        document.getElementById("gtl-label-count").textContent = 0;
    }
    sessionStorage.clear();
    console.log('session clear');
});

$(".gtl-chkbox").click(function(){
    if (typeof Storage !== "undefined") { // Support Web Storage
        if (sessionStorage.getItem("selected") === null){
            var item = [$(this).attr('data-value')];
            var name = [$(this).attr("data-name")];
            var path = [$(this).attr("data-path")];
            var path_name = [$(this).attr("data-path-name")];
            var conname = [$(this).attr("data-conname")];
            var size = [$(this).attr("data-size")];
            var date = [$(this).attr("data-date")];
            var icon = [$(this).attr("data-icon")];
            sessionStorage.setItem("selected",JSON.stringify(item));
            sessionStorage.setItem("selected_name",JSON.stringify(name));
            sessionStorage.setItem("selected_path",JSON.stringify(path));
            sessionStorage.setItem("selected_path_name",JSON.stringify(path_name));
            sessionStorage.setItem("selected_conname",JSON.stringify(conname));
            sessionStorage.setItem("selected_size", JSON.stringify(size));
            sessionStorage.setItem("selected_date", JSON.stringify(date));
            sessionStorage.setItem("selected_icon", JSON.stringify(icon));
        }else{
            var item = JSON.parse(sessionStorage.getItem("selected"));
            var name = JSON.parse(sessionStorage.getItem("selected_name"));
            var path = JSON.parse(sessionStorage.getItem("selected_path"));
            var path_name = JSON.parse(sessionStorage.getItem("selected_path_name"));
            var conname = JSON.parse(sessionStorage.getItem("selected_conname"));
            var size = JSON.parse(sessionStorage.getItem("selected_size"));
            var date = JSON.parse(sessionStorage.getItem("selected_date"));
            var icon = JSON.parse(sessionStorage.getItem("selected_icon"));
            //alert(ids);
            var index = item.indexOf($(this).attr("data-value"));
            if (index !== -1){
                item.splice(index,1);
                name.splice(index,1);
                path.splice(index,1);
                path_name.splice(index,1);
                conname.splice(index,1);
                size.splice(index,1);
                date.splice(index,1);
                icon.splice(index,1);
            }else{
                item.push($(this).attr("data-value"));
                name.push($(this).attr("data-name"));
                path.push($(this).attr("data-path"));
                path_name.push($(this).attr("data-path-name"));
                conname.push($(this).attr("data-conname"));
                size.push($(this).attr("data-size"));
                date.push($(this).attr("data-date"));
                icon.push($(this).attr("data-icon"));
            }
            //ids.sort();
            sessionStorage.setItem("selected",JSON.stringify(item));
            sessionStorage.setItem("selected_name",JSON.stringify(name));
            sessionStorage.setItem("selected_path",JSON.stringify(path));
            sessionStorage.setItem("selected_path_name",JSON.stringify(path_name));
            sessionStorage.setItem("selected_conname",JSON.stringify(conname));
            sessionStorage.setItem("selected_size",JSON.stringify(size));
            sessionStorage.setItem("selected_date",JSON.stringify(date));
            sessionStorage.setItem("selected_icon",JSON.stringify(icon));
            //alert(date);
        }
        document.getElementById("gtl-label-count").textContent = item.length;
    }
});

//$("#gtl-name").onchange(function(){
//    sessionStorage.setItem("gtl-name", $(this).value);
//});
//
//$("#gtl-name").ready(function(){
//    if (sessionStorage.getItem("name") !== null){
//        var gtlName = sessionStorage.getItem("gtl-name");
//        document.getElementById("gtl-label-name").value = gtlName;
//    }
//
//});

$("#gtl-btn-continue").click(function(e){
    var selected_ids = JSON.parse(sessionStorage.getItem("selected"));
    var gtlName = document.getElementById("gtl-name").value;
    if (selected_ids === null || selected_ids.length === 0){
        alert("Please Selected at least 1 item");
    }else if(gtlName === null || gtlName.trim() === ""){
        alert("Please fill out GTL Name");
    }else{
        var url = window.location.pathname + '/create';
        sessionStorage.setItem("gtl-name", gtlName);
        $("#board").load(url);
    }
});
//$("#gtl-btn-save").click(function(e){
//    $(".gtl-chkbox").attr('checked',false);
//    sessionStorage.removeItem("selected");
//    alert("Reset");
//});


// handlers
//function onGetClick(baseUrl) {
//    // we're not passing any data with the get route, though you can if you want
//    var path = $(this).attr('value');
//    var connName = $(this).attr('data-conname');
//
//    if (window.location.pathname.search("search") == -1){
//        var url = window.location.pathname + "?path=" + encodeURIComponent(dir)
//            + "&provider=" + encodeURIComponent(prov);
//    }else{
//        var path = window.location.pathname.replace('/search','');
//        var url = path + "?path=" + encodeURIComponent(dir) + "&provider=" + encodeURIComponent(prov);
//    }
//    url = url.replace('/gtl','/gtl/select');
//    //alert(url);
//    $("body").css("cursor", "progress");
//    $("#board").load(url);
//}
//
//function onPostClick(event) {
//    // we're passing data with the post route, as this is more normal
//    $.post('/ajax/post', {payload: 'hello'}, onSuccess);
//}
//
//function onSuccess(data, status, xhr) {
//    // with our success handler, we're just logging the data...
//    console.log(data, status, xhr);
//
//    // but you can do something with it if you like - the JSON is deserialised into an object
//    console.log(String(data.value).toUpperCase());
//    ;
//
//}

// listeners
//$('span#dir').on('click', onGetClick);
//$('button#post').on('click', onPostClick);
