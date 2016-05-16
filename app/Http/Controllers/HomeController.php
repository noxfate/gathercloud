<?php

namespace App\Http\Controllers;

use App\AppModels\Provider;
use App\DummyFile;
use App\Providers;
use App\Token;
use Illuminate\Http\Request;

use Auth;
use Symfony\Component\HttpFoundation\File\File;
use App\User;
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
                    foreach($temp as $index => $d){
                        if($d['path'] == $tk->gtc_folder){
                            unset($temp[$index]);
                        }
                    }
                    $data = array_merge($data, $temp);
                }
            } else {
                $proObj = new Provider($id);
                $data = $proObj->getFiles();
            }

            if(!empty($data)){
                foreach ($data as $key => $row) {
                    $is_dir[$key]  = $row['is_dir'];
                    $name[$key] = $row['name'];
                }

                array_multisort($is_dir, SORT_DESC, $name, SORT_ASC, $data);
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

        // dummy check
        $dummy_tk = Token::where('connection_name', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();
        $dummy_files = DummyFile::where('dummy_store',$dummy_tk->id)
            ->where('dummy_path', $any)
            ->get();
        if(!empty($dummy_files)){
            foreach($dummy_files as $d){
                $real_tk = Token::where('id', $d->real_store)
                    ->where('user_id', Auth::user()->id)
                    ->firstOrFail();
                $realProObj = new Provider($real_tk->connection_name);
                $temp = $realProObj->getFiles($d->path);
                $data = array_merge($data, $temp);
            }
        }


        if(!empty($data)){
            foreach ($data as $key => $row) {
                $is_dir[$key]  = $row['is_dir'];
                $name[$key] = $row['name'];
            }

            array_multisort($is_dir, SORT_DESC, $name, SORT_ASC, $data);
        }

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

    public function getStorages(){
        $tks = User::find(auth()->user()->id)->token->all();
        $storages = array();
        foreach($tks as $tk){
            $proObj = new Provider($tk->connection_name);
            $temp = $proObj->getAccountInfo();
            $temp = (array)$temp;
            $temp['connection_name'] = $tk->connection_name;
            $temp = (object)$temp;
            array_push($storages,$temp);

        }
        return json_encode($storages);
    }

    public function redundancyCheck(Request $req){
        if ($req->hasFile('file'))
        {
            $file = $req->file('file');
            $file_name =  $file->getClientOriginalName();
            $token = User::find(Auth::user()->id)->token;
            $data = array();
            foreach ($token as $tk) {
                $proObj = new Provider($tk->connection_name);
                $temp = $proObj->SearchFile($file_name);
                if(!empty($temp)){
                    array_push($data,$tk->connection_name);
                }
            }
            return json_encode($data);
        }
    }

    public function getFolderList(Request $req){
        $proObj = new Provider($req->input('connection_name'));
        $data = $proObj->getFiles($req->input('path'));
        foreach($data as $index => $d){
            if(!$d['is_dir']){
                unset($data[$index]);
            }
        }
        return json_encode($data);
    }

    /**
     *
     */


    public function getConnectionList(){
        $tokens = User::find(Auth::user()->id)->token;
        $data = array();
        foreach($tokens as $tk){
            $icon = Providers::where('id',$tk->provider_id)->first()->provider_logo;
            $d = array(
                'connection_name' => $tk->connection_name,
                'icon' => $icon
            );
            array_push($data,$d);
        }
        return json_encode($data);
    }

    public function transferFile(){
//        dump($_POST['tf_file']);
//        dump($_POST['from_connection']);
//        dump($_POST['to_connection_name']);
//        dump($_POST['to_path']);
        $proObj = new Provider($_POST['from_connection']);
        $filename = $proObj->downloadFile($_POST['tf_file'], 'temp');
        $proObj->deleteFile($_POST['tf_file']);
        $proObj = new Provider($_POST['to_connection_name']);
        $objfile = new File($filename);
        $file = array(
            'name' => $objfile->getBasename(),
            'type' => $objfile->getMimeType(),
            'tmp_name' => $filename,
            'error' => 0,
            'size' => $objfile->getSize()
        );
        $proObj->uploadFile($file,$_POST['to_path']);
        readfile($filename);
        unlink($filename);
    }

    public function test(){

         phpinfo();
    }

    public function download(){

        $proObj = new Provider($_GET['connection_name']);
        $proObj->downloadFile($_GET['file']);

    }

    public function upload(){
        $proObj = new Provider($_POST['connection_name']);
        return $proObj->uploadFile($_FILES['file'],$_POST['destination']);
    }
    public function createFolder(){
        $proObj = new Provider($_POST['connection_name']);
        return $proObj->uploadFile($_POST['name'],$_POST['destination']);
    }
    public function upload_dummy(){
        dump($_POST['real_store']);
        dump($_POST['dummy_path']);
        dump($_POST['dummy_store']);
        dump(User::find(Auth::user()->id)->token
            ->where('connection_name',$_POST['real_store'])
            ->first()->id
        );

        $tk = Token::where('connection_name', $_POST['real_store'])
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();
        $proObj = new Provider($_POST['real_store']);
        $path = $proObj->uploadFile($_FILES['file'], $tk->gtc_folder);
        if($_POST['dummy_store'] != 'all'){
            $dm = new DummyFile();
            $real_store = User::find(Auth::user()->id)->token
                ->where('connection_name',$_POST['real_store'])
                ->first()->id;
            $dummy_store = User::find(Auth::user()->id)->token
                ->where('connection_name',$_POST['dummy_store'])
                ->first()->id;
            $dm->path = $path[0]['path'];
            $dm->real_store = $real_store;
            $dm->dummy_path = $_POST['dummy_path'];
            $dm->dummy_store = $dummy_store;
            $dm->save();
        }

    }

    public function delete(){
        // Provider(" waiting edit with ALL")
        $proObj = new Provider($_POST['connection_name']);
        return $proObj->deleteFile($_POST['file']);
    }

    public function rename(){
        // Provider(" waiting edit with ALL")
        $proObj = new Provider($_POST['connection_name']);
        return $proObj->rename($_POST['file'], $_POST['new_name']);
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
