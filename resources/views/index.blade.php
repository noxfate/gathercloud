@extends("layout")

@section("content")
    <h2>GetFiles Dropbox</h2>
    @foreach($files as $file)

        NAME : {{ basename($file->path) }} <br>
        PATH : {{ $file->path }} <br>
        LAST MODIFIED : {{ $file->modified }} <br>
        @if($file->is_dir)
            <a href="#">Open Folder</a>  <br>
        @else
            SIZE : {{ $file->size }} <br>
            <a href="#">Download</a> <br>
            <a href="#">Share</a> <br>
            <a href="#">Delete</a> <br>
        @endif
        <br>


        "NAME : ". basename($file->path) . "<br>";
        echo "PATH : ". $file->path . "<br>";
        echo "LAST MODIFIED : ". $file->modified . "<br>";
        if ($file->is_dir){
        echo "<a href='#'>Open Folder</a><br>";
        }else{
        echo "SIZE : ". $file->size . "<br>";
        echo "<a href='controllers/download.php?dbxPath=". $file->path ."'>Download</a>";
        echo " <a href='". $dropbox->getLink($file->path) ."'>Share</a> ";
        echo " <a href='controllers/delete.php?dbxPath=". $file->path."'>Delete</a>";
        }
        echo "<br>";
    @endforeach

@endsection
