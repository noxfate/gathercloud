<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Token;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            return view('pages.index', [
                "data" => null,
                "cname" => "All"
            ]);
        } else
            return Redirect::to('/');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $que = Token::where('connection_name', $id)
            ->where('user_id', Auth::user()->id)
            ->get();
        if ($que->count() == 1) {
            $provider = $que[0]->provider;
        } else {
            return "Error: Connection_name is $id, COUNT : " . $que->count();
        }

        switch ($provider) {
            case "dropbox":
                $obj = new \App\Library\DropboxModel((array)\GuzzleHttp\json_decode($que[0]->access_token));
                break;
            case "copy":
                $obj = new \App\Library\CopyModel((array)\GuzzleHttp\json_decode($que[0]->access_token));
                break;
            default:
                return "Error!! Provider: $provider";
        }
        if (empty($_GET['path'])){
            $data = $obj->getFiles();
            $data = $this->normalizeMetaData($data,$provider);
            return view('pages.index', [
//            "data" => null,
                "data" => $data,
                "cname" => $id,
                "cmail" => $que[0]->id
            ]);
        }
        else{
            $data = $obj->getFiles("/".$_GET['path']);
            $data = $this->normalizeMetaData($data,$provider);
            return view('pages.board', [
//            "data" => null,
                "data" => $data,
                "cname" => $id.$_GET['path'],
                "cmail" => $que[0]->id
            ]);
        }
    }

    private function normalizeMetaData($data, $provider)
    {
//        $name = '';
//        $path = '';
//        $size = '';
//        $bytes = 0;
//        $mime_type = '';
//        $file_type = '';
//        $last_modified = '';
//        $shared = false;

        $format = array();

        switch ($provider){
            case "dropbox":
                foreach ($data as $k => $val) {
                    $val->is_dir == 1 ? $mime = null : $mime = $val->mime_type;
                    empty($val->shared_folder) ? $sh = false : $sh = true;
                    array_push($format,
                        array(
                            'name' => basename($k),
                            'path' => $val->path,
                            'size' =>  $val->size,
                            'bytes' => $val->bytes,
                            'mime_type' => $mime,
                            'is_dir' => $val->is_dir, // 1 == Folder, 0 = File
                            'modified' => $val->modified,
                            'shared' => $sh
                        ));
                }
                break;
            case "copy":
                foreach ($data as $k => $val) {
                    $val->type == "file"? $is = 0 : $is = 1;
                    $is == 1 ? $mime = null : $mime = $val->mime_type;
                    $val->share_id != 0 ? $sh = true : $sh = false;
                    array_push($format,
                        array(
                            'name' => basename($val->path),
                            'path' => $val->path,
                            'size' =>  $this->humanFileSize($val->size),
                            'bytes' => $val->size,
                            'mime_type' => $mime,
                            'is_dir' => $is, // 1 == Folder, 0 = File
                            'modified' => date('Y m d H:i:s', $val->modified_time),
                            'shared' => $sh
                        ));
                }
                break;
            default:
                return "Error!! Provider: $provider";
        }

        return $format;


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function humanFileSize($size)
    {
        if (!$size) {
            return "";
        } elseif (($size >= 1 << 30)) {
            return number_format($size / (1 << 30), 2) . "GB";
        } elseif (($size >= 1 << 20)) {
            return number_format($size / (1 << 20), 2) . "MB";
        } elseif (($size >= 1 << 10)) {
            return number_format($size / (1 << 10),2) . "kB";
        } else {
            return number_format($size) . "B";
        }
    }
}
