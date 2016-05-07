$("body").css("cursor", "default");
// set up jQuery with the CSRF token, or else post routes will fail
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

$(".gtl-chkbox").ready(function(){
    if (sessionStorage.getItem("selected") !== null){
        var items = JSON.parse(sessionStorage.getItem("selected"));
        document.getElementById("gtl-label-count").textContent = items.length;
        for (item in items){
            $("[data-conname='"+items[item]+"']").prop("checked",true);
        }
    }

});

$(".gtl-chkbox").click(function(){
    if (typeof Storage !== "undefined") { // Support Web Storage
        if (sessionStorage.getItem("selected") === null){
            var item = [$(this).attr("data-conname")];
            var size = [$(this).attr("data-size")];
            var date = [$(this).attr("data-date")];
            sessionStorage.setItem("selected",JSON.stringify(item));
            sessionStorage.setItem("selected_size", JSON.stringify(size));
            sessionStorage.setItem("selected_date", JSON.stringify(date));
        }else{
            var item = JSON.parse(sessionStorage.getItem("selected"));
            var size = JSON.parse(sessionStorage.getItem("selected_size"));
            var date = JSON.parse(sessionStorage.getItem("selected_date"));
            //alert(ids);
            var index = item.indexOf($(this).attr("data-conname"));
            if (index !== -1){
                item.splice(index,1);
                size.splice(index,1);
                date.splice(index,1);
            }else{
                item.push($(this).attr("data-conname"));
                size.push($(this).attr("data-size"));
                date.push($(this).attr("data-date"));
            }
            //ids.sort();
            sessionStorage.setItem("selected",JSON.stringify(item));
            sessionStorage.setItem("selected_size",JSON.stringify(size));
            sessionStorage.setItem("selected_date",JSON.stringify(date));
            //alert(date);
        }
        document.getElementById("gtl-label-count").textContent = item.length;
    }
});

$("#gtl-btn-save").click(function(e){
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
