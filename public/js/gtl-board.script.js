
$("body").css("cursor", "default");
// set up jQuery with the CSRF token, or else post routes will fail
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});


//$(".gtl-chkbox").hide();
//$("#gtl-btn-cancel").hide();
//$("#gtl-btn-save").hide();
//$("#gtl-btn").click(function(){
//    $("#gtl-btn-cancel").show();
//    $("#gtl-btn-save").show();
//    $(".gtl-chkbox").show();
//});
$(".gtl-chkbox").click(function(){
    if (typeof Storage !== "undefined") { // Support Web Storage
        if (sessionStorage.getItem("selected") === null){
            sessionStorage.setItem("selected",JSON.stringify([$(this).attr("id")]));
            // alert("Created");
        }else{
            var ids = JSON.parse(sessionStorage.getItem("selected"));
            var index = ids.indexOf($(this).attr("id"));
            if (index !== -1){
                ids.splice(index,1);
            }else{
                ids.push($(this).attr("id"));
            }
            ids.sort();
            sessionStorage.setItem("selected",JSON.stringify(ids));
            // alert(ids);
        }
    }
});
$("#gtl-btn-save").click(function(e){
    var selected_ids = JSON.parse(sessionStorage.getItem("selected"));
    if (selected_ids === null){
        alert("Please Selected at least 1 item");
    }else{
        window.location.href = window.location.pathname.replace('/home','/gtl/create');;
    }
});
//$("#gtl-btn-cancel").click(function(e){
//    $(".gtl-chkbox").attr('checked',false);
//    sessionStorage.removeItem("selected");
//    $(".gtl-chkbox").hide();
//    $("#gtl-btn-cancel").hide();
//    $("#gtl-btn-save").hide();
//});


// handlers
function onGetClick(event) {
    // we're not passing any data with the get route, though you can if you want
    var dir = $(this).attr('value');
    var prov = $(this).attr('alt');
    if (window.location.pathname.search("search") == -1){
        var url = window.location.pathname + "?path=" + encodeURIComponent(dir)
            + "&provider=" + encodeURIComponent(prov);
    }else{
        var path = window.location.pathname.replace('/search','');
        var url = path + "?path=" + encodeURIComponent(dir) + "&provider=" + encodeURIComponent(prov);
    }
    $("body").css("cursor", "progress");
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
    ;

}

// listeners
//                $('button#get').on('click', onGetClick);
$('span#dir').on('click', onGetClick);
$('button#post').on('click', onPostClick);
