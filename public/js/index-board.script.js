
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
    ;

}

function trig_uploadOnClick(){
$('.bar-percentage[data-percentage]').each(function () {
    var progress = $(this);
    var percentage = Math.ceil($(this).attr('data-percentage'));
    $({countNum: 0}).animate({countNum: percentage}, {
        duration: 2000,
        easing:'linear',
        step: function() {
            // What todo on every count
            var pct = Math.floor(this.countNum) + '%';
            progress.text(pct) && progress.siblings().children().css('width',pct);
        }
    });
});
}

document.getElementById("file").onchange = function () {
    document.getElementById("file-selected").innerHTML = this.value.replace("C:\\fakepath\\", "");
    document.getElementById('panel-priority').style.display = 'inherit';
    trig_uploadOnClick();
};

// listeners
// $('button#get').on('click', onGetClick);
//$('span#dir').on('click', onGetClick);
$('button#post').on('click', onPostClick);
