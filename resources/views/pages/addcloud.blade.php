@extends('layout.layout-of-index')

@section('content')
    <div id="board" class="board">
        <a href="add/dropbox">+Dropbox</a> || <a href="add/copy">+Copy</a> ||
        <a href="">+Box</a> || <a href="">+OneDrive</a>


        <!-- Img trigger modal -->
        <div class="div-circle" data-toggle="modal" data-target="#myModal">
            <img src="{{ URL::asset('images/logo-copy.png') }}">
        </div>
        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="add/copy" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Create Connection to copy account.</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="div-circle">
                                    <img src="{{ URL::asset('images/logo-copy.png') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4>Connection Name:</h4>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="text" name="conname" id="conname" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" id="create-copy" value="Create">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

    </div>
    <script>
        document.getElementById('add-cloud').style.backgroundColor = "white";
    </script>
@endsection