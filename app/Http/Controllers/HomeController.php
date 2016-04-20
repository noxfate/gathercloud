<?php

namespace App\Http\Controllers;

use App\AppModels\Provider;
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
                $token = User::find(Auth::user()->id)->tokens;
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
                'in' => $id
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
        $par = (object)array(
            'pname' => array(),
            'ppath' => array(),
            'pprovider' => array()
        );
        $parent = $this->getNavbar($cname,$proObj->getPathName($any),$any);
        return view('pages.cloud.index',[
            'data' => $data,
            "cname" => $cname,
            'parent' => $parent,
            'in' => $id
        ]);
    }

    private function getNavbar($id,$pathname,$path)
    {
        $pathname = $id . (($pathname == "")? "" : "/".$pathname);
        $path = $id . (($pathname == "")? "" : "/".$path);
        $parent = (object)array(
            'par_now' => "",
            'par_name' => array(),
            'par_path' => array()
        );
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
        $parent->par_now = end($parent->par_path);
        return $parent;
    }

    public function download(){

        $proObj = new Provider($_GET['connection_name']);
        $proObj->downloadFile($_GET['file']);

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
    }

    public function search()
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

}
