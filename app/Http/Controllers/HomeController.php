<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Token;
use App\Cache;
use App\Jobs\CreateFileMapping;
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

            $que = Cache::where('user_id',Auth::user()->id)->get();
            $data = array();
            foreach ($que as $d ) {
                $inside_json = json_decode($d->data, true);
                foreach ($inside_json as $in){
                    array_push($data, $in);
                }
                
                
            }
            // dd($data);
            return view('pages.index',[
                'data' => $data,
                'cmail' => 'aaa@hotmail.com' // Hard code as Fuck
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
                $obj = new \App\Library\DropboxInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
                break;
            case "copy":
                $obj = new \App\Library\CopyInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
                break;
            case "box":
                $obj = new \App\Library\BoxInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
                break;
            case "onedrive":
                $obj = new \App\Library\OneDriveInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
                break;
            default:
                return "Error!! Provider: $provider";
        }

        $job = (new CreateFileMapping($obj,$provider));
        $this->dispatch($job);

        $cac = Cache::where('user_id',Auth::user()->id)
            ->where('provider',$provider)
            ->where('user_connection_name',$id)
            ->get();
        if ($cac->count() == 0){
            $cac = new Cache();
            $cac->user_id = Auth::user()->id;
            $cac->provider = $provider;
            $cac->user_connection_name = $id;
        }else if ($cac->count() == 1){
            $cac = $cac->first();
        }

        if (empty($_GET['path'])) {
            $data = $obj->getFiles();
            $parent = $this->navbarDataByPath($id,"");
            $data = $this->normalizeMetaData($data, $provider);
            
            $cac->data = json_encode($data);
            $cac->save();


            return view('pages.index', [
//            "data" => null,
                "data" => $data,
                "cname" => $id,
                "cmail" => $que[0]->id,
                "parent" => $parent
            ]);
        } else {
            $data = $obj->getFiles($_GET['path']);
//            $parent = $this->navbarDataByPath($id.$_GET['path']);
            if ($provider == 'dropbox' || $provider == 'copy') {
                $parent = $this->navbarDataByPath($id,$_GET['path']);
            } elseif(($provider == 'box' || $provider == 'onedrive')) {
                $parent = $this->navbarDataById($id, $_GET['path'], $obj,$provider);
            }
            $data = $this->normalizeMetaData($data, $provider);


            print_r($_GET['path']);

            return view('pages.board', [
//            "data" => null,
                "data" => $data,
                "cname" => $id . $_GET['path'],
                "cmail" => $que[0]->id,
                "parent" => $parent
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

        switch ($provider) {
            case "dropbox":
                foreach ($data as $k => $val) {
                    $val->is_dir == 1 ? $mime = null : $mime = $val->mime_type;
                    empty($val->shared_folder) ? $sh = false : $sh = true;
                    array_push($format,
                        array(
                            'name' => basename($k),
                            'path' => $val->path,
                            'size' => $val->size,
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
                    $val->type == "file" ? $is = 0 : $is = 1;
                    $is == 1 ? $mime = null : $mime = $val->mime_type;
                    $val->share_id != 0 ? $sh = true : $sh = false;
                    array_push($format,
                        array(
                            'name' => basename($val->path),
                            'path' => $val->path,
                            'size' => $this->humanFileSize($val->size),
                            'bytes' => $val->size,
                            'mime_type' => $mime,
                            'is_dir' => $is, // 1 == Folder, 0 = File
                            'modified' => date('Y m d H:i:s', $val->modified_time),
                            'shared' => $sh
                        ));
                }
                break;
            case "box":
                foreach ($data as $k => $val) {
//                    $val->type == "file"? $is = 0 : $is = 1;
//                    $is == 1 ? $mime = null : $mime = $val->mime_type;
//                    $val->share_id != 0 ? $sh = true : $sh = false;
                    array_push($format,
                        array(
                            'name' => $val['name'],
                            'path' => $val['path'],
                            'size' => $this->humanFileSize($val['size']),
                            'bytes' => $val['size'],
                            'mime_type' => $val['mime_type'],
                            'is_dir' => $val['is_dir'],
                            'modified' => date('Y m d H:i:s', strtotime($val['modified'])),
                            'shared' => $val['shared']
                        ));
                }
                break;
            case "onedrive":
                foreach ($data as $val) {
                    $val->isFolder() ? $is = 1 : $is = 0;
//                    $is == 1 ? $mime = null : $mime = $val->mime_type;
//                    $val->share_id != 0 ? $sh = true : $sh = false;
                    array_push($format,
                        array(
                            'name' => basename($val->getName()),
                            'path' => $val->getId(),
                            'size' => $this->humanFileSize($val->getSize()),
                            'bytes' => $val->getSize(),
                            'mime_type' => null,
                            'is_dir' => $is, // 1 == Folder, 0 = File
                            'modified' => date('Y m d H:i:s', $val->getUpdatedTime()),
                            'shared' => false
                        ));
                }
                break;
            default:
                return "Error!! Provider: $provider";
        }

        return $format;


    }

    private function navbarDataByPath($id,$path)
    {
        $path = $id . $path;
        $parent = (object)array(
            'pname' => array(),
            'ppath' => array()
        );
        $parent->pname = explode("/", $path);
        $temp = '/';
        for ($i = 0; $i < count($parent->pname); $i++) {
            if ($i == 0) {
                $temp = $temp . $parent->pname[$i];
                $parent->ppath[] = $temp;
                $temp = '/';
            } else {
                $temp = $temp . $parent->pname[$i];
                $parent->ppath[] = $temp;
                $temp = $temp . '/';
            }

        }
        return $parent;
    }

    private function navbarDataById($id, $path, $obj, $provider)
    {
        $parent = (object)array(
            'pname' => array(),
            'ppath' => array()
        );


        if ($provider == 'box') {

            $parent->pname[] = $id;
            $parent->ppath[] = '/' . $id;

            $entity = $obj->getEntity($path);
            $pcollection = $entity->path_collection;
            for($i = 1; $i < $pcollection->total_count ; $i++){
                $parent->pname[] = $pcollection->entries[$i]->name;
                $parent->ppath[] = ($pcollection->entries[$i]->type == 'folder') ? 'folder.'.$pcollection->entries[$i]->id : 'file.'.$pcollection->entries[$i]->id;
            }
            $parent->pname[] = $entity->name;
            $parent->ppath[] = $entity->type . "." . $entity->id;

        }elseif($provider == 'onedrive'){
            $entity = $obj->getFolder($path);
            while($entity->getParentId() != null){
                $parent->pname[] = $entity->getName();
                $parent->ppath[] = $entity->getId();
                $entity = $obj->getFolder($entity->getParentId());
            }
            $parent->pname[] = $id;
            $parent->ppath[] = '/' . $id;

            $parent->pname = array_reverse($parent->pname);
            $parent->ppath = array_reverse($parent->ppath);
        }
        return $parent;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        //
    }

    private
    function humanFileSize($size)
    {
        if (!$size) {
            return "";
        } elseif (($size >= 1 << 30)) {
            return number_format($size / (1 << 30), 2) . "GB";
        } elseif (($size >= 1 << 20)) {
            return number_format($size / (1 << 20), 2) . "MB";
        } elseif (($size >= 1 << 10)) {
            return number_format($size / (1 << 10), 2) . "kB";
        } else {
            return number_format($size) . "B";
        }
    }
}
