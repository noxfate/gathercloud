<?php

namespace App\Http\Controllers;

use App\AppModels\Provider;
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
//                foreach( $que as $d){
//                    if ($d->cache === null){
//                        $job = (new CreateFileMapping($d->connection_name));
//                        $this->dispatch($job);
//                    }elseif (Carbon::now() >= $d->cache->updated_at->addMinutes(24*60)){
//                        $job = (new CreateFileMapping($d->connection_name));
//                        $this->dispatch($job);
//                    }
//                }

                $data = $fmap->getFirstLevel();
//                dd($data);
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

    public function test()
    {
//        $root = File::create(['fname'=> 'Root Node 2', 'token_id' => 1 ]);
//        $c1 = $root->children()->create(['fname' => 'Child #1', 'token_id' => 1]);
//        $c2 = File::create(['fname'=> 'Child #2', 'token_id' => 1 ]);
//        $c2->makeChildOf($root);


//        $r = File::roots()->where('token_id', 1)->firstOrFail();

//        $root = File::create(['name' => 'The Root of All Evil', 'token_id'=> 1]);
//
//        $dragons = File::create(['name' => 'Here Be Dragons', 'token_id'=>1]);
//        $dragons->makeChildOf($root);
//
////        File::allLeaves()->delete();
//
//        $monsters = new File(['name' => 'Horrible Monsters', 'token_id'=>1]);
//        $monsters->save();
////
//        $monsters->makeChildOf($dragons);
//
//        $demons = Creatures::where('name', '=', 'demons')->first();
//        $demons->moveToLeftOf($dragons);

//        $root = File::root();
//        $root->delete();
//        $des = $root->children()->get();
//        foreach ($des as $d) {
//            if ($d->isLeaf()){
//                echo "File!! : ". $d['name'];
//            }else{
//                echo $d->getImmediateDescendants();
//            }
//            echo "<br>";
//
//        }
//
//        dd($des);
    }

}
