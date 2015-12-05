<table class="table-body table-hover table-striped" >
    <button id="get">get test</button>
    <button id="post">post test</button>
    @if (!empty($data))
        @foreach($data as $d => $val)
            <tr class="withItemMenu">
                <td class="th-icon-cloud"><span class="glyphicon glyphicon-cloud"></span></td>
                <td class="th-name">
                    @if ($val['is_dir'])
                        <span class="glyphicon glyphicon-folder-close"></span>
                        {{--<a id="dir" href="{{ Request::url()."/".$val['name'] }}">{{ $val['name'] }}</a></td>--}}
                        <span id="dir" value="{{ $val['path'] }}">{{ $val['name'] }}</span></td>
                @else
                    <a href="#">{{ $val['name'] }}</a></td>
                @endif
                @if ($val['is_dir'])
                    <td class="th-size"></td>
                @else <td class="th-size">{{ $val['size'] }}</td>
                @endif
                <td class="th-last-mo">{{ $val['modified'] }}</td>
                <td class="th-action"><span class="caret action"></span></td>
            </tr>
        @endforeach
    @endif
</table>

<script>

    // set up jQuery with the CSRF token, or else post routes will fail
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // handlers
    function onGetClick(event)
    {
        // we're not passing any data with the get route, though you can if you want
        var dir = $(this).attr('value');
//                    $.get(window.location.href+"?path="+dir, onSuccess);
        var url = window.location.href+"?path="+encodeURIComponent(dir);
        alert(dir);
        $(".board-body").load(url);
    }

    function onPostClick(event)
    {
        // we're passing data with the post route, as this is more normal
        $.post('/ajax/post', {payload:'hello'}, onSuccess);
    }

    function onSuccess(data, status, xhr)
    {
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

</script>
@extends("components.contextmenu")