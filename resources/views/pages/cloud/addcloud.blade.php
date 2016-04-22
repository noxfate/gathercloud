@extends('layouts.master-index')

@section('content')
    <div id="board" class="board">
        <div id="board-add-cloud" class="board-add-cloud">
            <h1>Select Cloud Storage</h1>
            <!-- Img trigger modal Box -->
            <div class="div-circle" data-toggle="modal" data-target="#modal-box">
                <img src="{{ URL::asset('images/logo-provider/logo-box.png') }}">
            </div>
            <!-- Modal -->
            <div class="modal fade bs-example-modal-lg" id="modal-box" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <!-- Modal content-->
                    <form action="add/Box" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Create Connection to Box account.</h4>
                            </div>
                            <div class="modal-body">
                                <div class="div-center">
                                    <div class="div-circle">
                                        <img src="{{ URL::asset('logo-provider') }}">
                                    </div>
                                    <h5>Connection Name:</h5>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="text" name="connection_name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-primary" id="create-copy" value="Create">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <!-- Img trigger modal Dropbox -->
            <div class="div-circle" data-toggle="modal" data-target="#modal-dropbox">
                <img src="{{ URL::asset('images/logo-provider/logo-dropbox.png') }}">
            </div>
            <!-- Modal -->
            <div class="modal fade bs-example-modal-lg" id="modal-dropbox" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <!-- Modal content-->
                    <form action="add/Dropbox" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Create Connection to Dropbox account.</h4>
                            </div>
                            <div class="modal-body">
                                <div class="div-center">
                                    <div class="div-circle">
                                        <img src="{{ URL::asset('images/logo-provider/logo-dropbox.png') }}">
                                    </div>
                                    <h5>Connection Name:</h5>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="text" name="connection_name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-primary" id="create-copy" value="Create">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <!-- Img trigger modal Google Drive -->
            <div class="div-circle" data-toggle="modal" data-target="#modal-googledrive">
                <img src="{{ URL::asset('images/logo-provider/logo-googledrive.png') }}">
            </div>
            <!-- Modal -->
            <div class="modal fade bs-example-modal-lg" id="modal-googledrive" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <!-- Modal content-->
                    <form action="add/GoogleDrive" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Create Connection to Google Drive account.</h4>
                            </div>
                            <div class="modal-body">
                                <div class="div-center">
                                    <div class="div-circle">
                                        <img src="{{ URL::asset('images/logo-provider/logo-googledrive.png') }}">
                                    </div>
                                    <h5>Connection Name:</h5>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="text" name="connection_name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-primary" id="create-copy" value="Create">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <!-- Img trigger modal OneDrive -->
            <div class="div-circle" data-toggle="modal" data-target="#modal-onedrive">
                <img src="{{ URL::asset('images/logo-provider/logo-onedrive.png') }}">
            </div>
            <!-- Modal -->
            <div class="modal fade bs-example-modal-lg" id="modal-onedrive" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <!-- Modal content-->
                    <form action="add/OneDrive" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Create Connection to OneDrive account.</h4>
                            </div>
                            <div class="modal-body">
                                <div class="div-center">
                                    <div class="div-circle">
                                        <img src="{{ URL::asset('images/logo-provider/logo-onedrive.png') }}">
                                    </div>
                                    <h5>Connection Name:</h5>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="text" name="connection_name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-primary" id="create-copy" value="Create">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

        </div>
    </div>
    </div>
    <script>
        document.getElementById('add-cloud').style.backgroundColor = "white";
    </script>
@endsection