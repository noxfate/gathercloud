<?php

namespace App\Http\Controllers;

use App\AppModels\Provider;
use App\DummyFile;
use App\Token;
use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($id)
    {
        if (Auth::check()) {
            $cname = $id;
            if ($id == 'all') {
                $token = User::find(Auth::user()->id)->token;
                $data = array();
                foreach ($token as $tk) {
                    $proObj = new Provider($tk->connection_name);
                    $temp = $proObj->getFiles();
                    $data = array_merge($data, $temp);
                }
            } else {
                $proObj = new Provider($id);
                $data = $proObj->getFiles();
            }
            $parent = $this->getNavbar($cname,"","");
            return view('pages.cloud.index', [
                'data' => $data,
                "cname" => $cname,
                'parent' => $parent,
                'in' => $id,
                'upload_storages' => $this->getStorages()
            ]);
        } else return Redirect::to('/');
    }

    public function show($id,$any)
    {
        $cname = $id;
        if($id == 'all') {
            $id = $_GET['in'];
        }
        $proObj = new Provider($id);
        $data = $proObj->getFiles("/" . $any);
        $parent = $this->getNavbar($cname,$proObj->getPathName($any),$any);
        return view('pages.cloud.index',[
            'data' => $data,
            "cname" => $cname,
            'parent' => $parent,
            'in' => $id,
            'upload_storages' => $this->getStorages()
        ]);
    }

    private function getNavbar($id,$pathname,$path)
    {
        $parent = (object)array(
            'par_now' => "",
            'par_name' => array(),
            'par_path' => array()
        );
        $parent->par_now = $path;
        $pathname = $id . (($pathname == "")? "" : "/".$pathname);
        $path = $id . (($pathname == "")? "" : "/".$path);
        $parent->par_name = explode("/", $pathname);
        $paths = explode("/", $path);
        $temp = '/';
        for ($i = 0; $i < count($parent->par_name); $i++) {
            if ($i == 0) {
                $temp = $temp . $paths[$i];
                $parent->par_path[] = $temp;
                $temp = '/' . $id . '/';
            } else {
                $temp = $temp . $paths[$i];
                $parent->par_path[] = $temp;
                $temp = $temp . '/';
            }
        }
        return $parent;
    }

    private function getStorages(){
        $conn = User::find(auth()->user()->id)->token->all();
        return $conn;
    }

    public function download(){

        $proObj = new Provider($_GET['connection_name']);
        $proObj->downloadFile($_GET['file']);

    }

    public function upload(){
        $proObj = new Provider($_POST['connection_name']);
        $proObj->uploadFile($_FILES['file'],$_POST['destination']);
    }
    public function upload_dummy(){
        dump($_POST['real_store']);
        dump($_POST['dummy_path']);
        dump($_POST['dummy_store']);
        dump(User::find(Auth::user()->id)->token
            ->where('connection_name',$_POST['real_store'])
            ->first()->id
        );
        $proObj = new Provider($_POST['real_store']);
        $path = $proObj->uploadFile($_FILES['file']);
        if($_POST['dummy_store'] != 'all'){
            $dm = new DummyFile();
            $real_store = User::find(Auth::user()->id)->token
                ->where('connection_name',$_POST['real_store'])
                ->first()->id;
            $dummy_store = User::find(Auth::user()->id)->token
                ->where('connection_name',$_POST['dummy_store'])
                ->first()->id;
            $dm->path = $path;
            $dm->real_store = $real_store;
            $dm->dummy_path = $_POST['dummy_path'];
            $dm->dummy_store = $dummy_store;
            $dm->save();
        }

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
    }

    public function search($id)
    {
        if ($id == 'all') {
            $token = User::find(Auth::user()->id)->token;
            $data = array();
            foreach ($token as $tk) {
                $proObj = new Provider($tk->connection_name);
                $temp = $proObj->SearchFile($_GET['keyword']);
                $data = array_merge($data, $temp);
            }
        } else {
            $proObj = new Provider($id);
            $data = $proObj->SearchFile($_GET['keyword']);
        }
        $parent = $this->getNavbar("Results of '".$_GET['keyword']."'","","");
        return view('pages.cloud.index',[
            'data' => $data,
            "cname" => $id,
            'parent' => $parent,
            'in' => $id
        ]);
    }

}
