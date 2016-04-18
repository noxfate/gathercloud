<?php

namespace App\Http\Controllers;

use App\AppModels\Provider;
use App\Jobs\UpdateFileMapping;
use Illuminate\Http\Request;

use Auth;
use App\File;
use App\User;
use App\Token;
use App\Cache;
use App\Jobs\CreateFileMapping;
use Carbon\Carbon;
use App\Library\FileMapping;
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

            $que = User::find(Auth::user()->id)->tokens;
            $email = User::find(Auth::user()->id)->email;

            $fmap = new FileMapping(Auth::user()->id);

            // All in One without Ajax Request
            if (empty($_GET['path'])){

//                Create Cache when enter /home at All in One
                $token = User::find(Auth::user()->id)->tokens;
                foreach( $token as $tk){
//                    $job = (new CreateFileMapping($tk->connection_name));
//                    $this->dispatch($job);
                    $root = File::roots()->where('token_id', $tk->id)->first();
                    if (Carbon::now() >= $root->updated_at->addMinutes(24*60)){
                        $job = (new CreateFileMapping($tk->connection_name));
                        $this->dispatch($job);
                    }
                }

                $data = $fmap->getFirstLevel();
                $par = $this->navbarDataByPath("All","");
                return view('pages.cloud.index',[
                    'data' => $data,
                    "cname" => "all",
                    'cmail' => $email,
                    'parent' => $par
                ]);
            }else{
                $data = $fmap->traverseInsideFolder($_GET['path'], $_GET['connid']);
                $par = $this->navbarDataByPath("All",$_GET['path']);
                return view('pages.cloud.components.index-board',[
                    'data' => $data,
                    "cname" => "all",
                    'cmail' => $email,
                    'parent' => $par
                ]);
            }


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
        $proObj = new Provider($id);
        $provider = $proObj->getProvider();

        // if at 1st level Folder, No Ajax request.
        if (empty($_GET['path'])) {

            $data = $proObj->getFiles();
            $parent = $this->navbarDataByPath($id,"");

            return view('pages.cloud.index', [
//            "data" => null,
                "data" => $data,
                "cname" => $id,
                "parent" => $parent
            ]);
        } else {
            $data = $proObj->getFiles($_GET['path']);
            // dd($data)
            if ($provider == 'dropbox' || $provider == 'copy') {
                $parent = $this->navbarDataByPath($id,$_GET['path']);
            } elseif(($provider == 'box' || $provider == 'onedrive')) {
                $parent = $this->navbarDataById($id, $_GET['path'], $proObj,$provider);
            }
            return view('pages.cloud.components.index-board', [
//            "data" => null,
                "data" => $data,
                "cname" => $id,
                "parent" => $parent
            ]);
        }
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

    public
    function search()
    {
        $fmap = new FileMapping(Auth::user()->id);
        $result = $fmap->searchFiles($_GET['keyword']);

        $email = User::find(Auth::user()->id)->email;

        // All in One without Ajax Request
        if (empty($_GET['path'])){
            $par = $this->navbarDataByPath("All","");
            return view('pages.cloud.index',[
                'data' => $result,
                "cname" => "All",
                'cmail' => $email,
                'parent' => $par
            ]);
        }else{
            $data = $fmap->traverseInsideFolder($_GET['path'], $_GET['connid']);
            $par = $this->navbarDataByPath("All",$_GET['path']);
            return view('pages.cloud.components.index-board',[
                'data' => $data,
                "cname" => "All",
                'cmail' => $email,
                'parent' => $par
            ]);
        }
    }

    private function navbarDataByPath($id,$path)
    {
        $path = $id . $path;
        $parent = (object)array(
            'pname' => array(),
            'ppath' => array(),
            'pprovider' => array()
        );
        if (!empty($_GET['connid'])){
            $parent->pprovider = $_GET['connid'];
        }
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


    public function download(){

//        $que = Token::where('connection_name',  $_GET['connection_name'])
//            ->where('user_id', Auth::user()->id)
//            ->get();
//        if ($que->count() == 1) {
//            $provider = $que[0]->provider;
//        } else if ($_GET['connection_name'] == "All"){
//            return Redirect::to('/home');
//        } else {
//            return "Error: Connection_name is " + $_GET['connection_name'] +", COUNT : " . $que->count();
//        }
//
//        switch ($provider) {
//            case "dropbox":
//                $obj = new \App\Library\DropboxInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
//                break;
//            case "copy":
//                $obj = new \App\Library\CopyInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
//                break;
//            case "box":
//                $obj = new \App\Library\BoxInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
//                break;
//            case "onedrive":
//                $obj = new \App\Library\OneDriveInterface((array)\GuzzleHttp\json_decode($que[0]->access_token));
//                break;
//            default:
//                return "Error!! Provider: $provider";
//        }

        $proObj = new Provider($_GET['connection_name']);
        $proObj->downloadFile($_GET['file']);
//        $obj->downloadFile($_GET['file']);
//        header("Content-Type: application/download");
//        header("Content-disposition: attachment; filename=". basename($_GET['file']));
//        readfile(basename($_GET['file']));
//        unlink(basename($_GET['file']));
    }

    public function upload(){
        $proObj = new Provider($_GET['connection_name']);
        $proObj->uploadFile($_GET['file']);
    }

    public function delete(){
        // Provider(" waiting edit with ALL")
        $proObj = new Provider($_POST['connection_name']);
        $proObj->deleteFile($_POST['file']);
        return "test--";
    }

    public function rename(){
        // Provider(" waiting edit with ALL")
        $proObj = new Provider($_POST['connection_name']);
        $proObj->rename($_POST['file'], $_POST['new_name']);
        return "test--";
//        return $_POST['new_name'] . " + " . $_POST['file'];
    }

    public function test()
    {
//        $prov = new Provider(Token::find(7)->connection_name);
////        $prov = new Provider(Token::find(3)->connection_name);
//        $s = json_decode($prov->getAccountInfo());
//        dd($s);
//        foreach( $s->storage as $key => $val){
//            echo $key. " : ". $val;
//            echo "<br>";
//        }

        $tk = User::find(Auth::user()->id)->tokens;
//        foreach ($tk as $t){
//            $pro = new Provider($t->connection_name);
//            dd($pro->getStorage(true));
//        }
        $pro = new Provider($tk[1]->connection_name);
        dd($pro->getStorage(true));
    }

}
