<!-- Change to layouts.layouts-of-setting bar -->
@extends('layouts.master-index')
@section('content')
    <h1>Cloud Connection Setting</h1>
    E-mail: {{ $user->email }} <br><br>

    <table id="conn-table" border="2">
        <tr>
            <td>Connection Name</td>
            <td>Connection Email</td>
            <td>Provider</td>
            <td>Last Updated</td>
            <td>Status</td>
            <td></td>
        </tr>
        @foreach( $conn as $c )
        <tr>
            <td class="conn" value="{{ $c->id }}">{{ $c->connection_name }}</td>
            <td>{{ $c->connection_email }}</td>
            <td>{{ $c->provider  }}</td>
            <td>{{ $c->updated_at  }}</td>
            <td><button class="disBtn" id="dis{{ $c->id }}" data-token="{{ csrf_token() }}">Disconnect</button></td>
            <td><button class="saveBtn" id="saveBtn{{ $c->id }}" data-token="{{ csrf_token() }}" disabled>Save</button></td>
        </tr>
        @endforeach
    </table>


<script>

    $(".disBtn").click(function(){

        alert("This Connection will be Disconnect. Are you sure?");
        var connId = $(this).attr("id");
        connId = connId.substr(3);
        var url = window.location.pathname + "/" + connId;
        $.ajax({
            type: 'DELETE',
            url: url,
            data: { 
                _token: $(this).data('token')
            },
            success: function(response){
                // Show Successful Feedback
                alert("response: "+response);
                // do more stuff
            }
        });
    });

    $(".conn").dblclick(function(){
        var id = $(this).attr("value");
        $(this).html("<input type='text' id='txtbox' value='"+ $(this).text() +"'>");
        $("input#txtbox").on("keypress", function(e){
            if (e.keyCode == 13){
                // alert(id);
                $(this).attr("disabled", "disabled");
                $("button#saveBtn"+id).attr("value", $(this).val());
                $("button#saveBtn"+id).removeAttr("disabled");
            }
        });
    });

    $(".saveBtn").click(function(e){
        var connId = $(this).attr("id");
        connId = connId.substr(7);
        var connName = $(this).val();
        var url = window.location.pathname + "/" + connId;
        $.ajax({
            type: 'PUT',
            url: url,
            data: { 
                _token: $(this).data('token'),
                rename: connName 
            },
            success: function(response){
                // Show Successful Feedback
                alert("response: "+response);
                // do more stuff
            }
        });
        
    });

</script>
@endsection