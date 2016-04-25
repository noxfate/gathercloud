<!-- Change to layouts.layouts-of-setting bar -->
@extends('layouts.master-index')
@section('content')
    <div class="board">
        <div class="setting-box">
            <div class="form-horizontal">
                <fieldset>
                    <legend><h4><span class="label label-primary">Cloud Connection Setting</span></h4></legend>
                </fieldset>
                <div style="padding: 0px 20px">
                    <table class="table table-striped table-hover" id="conn-table">
                        <thead>
                        <tr>
                            <th class="text-primary">#</th>
                            <th class="text-primary">Connection Name</th>
                            <th class="text-primary">Connection Email</th>
                            <th class="text-primary">Provider</th>
                            <th class="text-primary">Last Updated</th>
                            <th></th>
                            {{--<th></th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $conn as $index =>$c )
                            <tr>
                                <td style="vertical-align: middle">{{$index+1}}</td>
                                <td style="vertical-align: middle" value="{{ $c->id }}">{{ $c->connection_name }}</td>
                                <td style="vertical-align: middle">{{ $c->connection_email }}</td>
                                <td style="vertical-align: middle">Dropbox</td>
                                <td style="vertical-align: middle">{{ $c->updated_at  }}</td>
                                <td style="vertical-align: middle"><button class="btn btn-danger" id="dis{{ $c->id }}" data-token="{{ csrf_token() }}">Disconnect</button></td>
                                {{--<td><button class="saveBtn" id="saveBtn{{ $c->id }}" data-token="{{ csrf_token() }}" disabled>Save</button></td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(".disBtn").click(function(){

            confirm("This Connection will be Disconnect. Are you sure?");
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