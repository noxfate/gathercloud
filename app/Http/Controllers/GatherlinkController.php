<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Providers;
use App\File;
use App\Link;
use App\DummyFile;
use App\AppModels\Provider;
use App\Jobs\CreateFileMapping;
use App\Library\FileMapping;
use App\Http\Requests;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Redirect;

class GatherlinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()){
            return view('pages.gtl.gtl-landing');
        }
        return Redirect::to("/");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::check())
            return Redirect::to('/');

        return view('pages.gtl.components.gtl-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $items = (array)json_decode($request->input('items'));
        $connections_name = json_decode($request->input('connections_name'));
        $names = json_decode($request->input('names'));
        $sizes = json_decode($request->input('sizes'));
        $dates = json_decode($request->input('dates'));
        $path_names = json_decode($request->input('path_name'));
        $tmp_url = substr(base64_encode(sha1(mt_rand())), 0, 16);
        $link = new Link();
        $link->link_name = $request->input('lkname');
        $link->user_id = Auth::user()->id;
        $link->url = $tmp_url;
        $link->save();
        $lid = $link->id;
        $random = User::find(Auth::user()->id)->token->first()->id;
        $root = File::create([
            'name' => 'root',
            'path' => $request->input("lkname"),
            'link_id' => $lid,
            'token_id' => $random
            // Error
        ]);

        foreach ($items as $index => $item){
            $tokenName = $connections_name[$index];
            $name = $names[$index];
            $path = $item;
            $size = $sizes[$index];
            $date = $dates[$index];
            $provObj = new Provider($tokenName);
            $share_link = $provObj->getLink($path);
//            $before_dir = str_replace('/'.basename($path), "", $path);
            $root->children()->create([
                'name' => $name,
                'path' => $path_names[$index],
                'bytes' => 0,
                'size' => $size,
                'mime_type' => "",
                'is_dir' => ($size == "")? true:false,
                'shared' => $share_link,
                'modified' => $date,
                'token_id' => $provObj->getTokenId(),
                'link_id'=> $lid
            ]);
        }
        return Redirect::to('/gtl/'.$request->input("lkname"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::check()){
            $link = Link::where('link_name',$id)->where('user_id', Auth::user()->id)->first();
//            dd($link);
            $root = File::roots()->where('link_id', $link->id)->first();
            $data = $root->getDescendants();
//            foreach($link as $d){
//                $data = $data->push(File::find($d->file_id));
//            }
//            dd($data);
            $logo = array();
            foreach($data as $d){
                array_push($logo,Providers::where('id',Token::where('id',$d->token_id)->first()->provider_id)->first()->provider_logo);
            }
            $con = array();
            foreach($data as $d){
                array_push($con,Token::where('id',$d->token_id)->first()->connection_name);
            }
            return view("pages.gtl.gtl-index",[
                "link" => $link,
                "data" => $data,
                'logo' => $logo,
                'con' => $con,
                'key' => $id
            ]);

        }
        return Redirect::to('/');
    }

    public function showFromToken()
    {
        $link = Link::where("url",$_GET['tokens'])->first();
        $root = File::roots()->where('link_id', $link->id)->first();
        $usr = User::where('id',$link->user_id)->first();
        $data = $root->getDescendants();
        $logo = array();
        foreach($data as $d){
            array_push($logo,Providers::where('id',Token::where('id',$d->token_id)->first()->provider_id)->first()->provider_logo);
        }
        return view('pages.gtl.gtl-another-seen',[
            'data' => (object)$data,
            'logo' => $logo,
            'lname' => $link->link_name,
            'usr' => $usr
        ]);

        // Show GatherLink Owner name na ja.
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
        $link = Link::where('link_name',$id)->where('user_id', Auth::user()->id)->first();
        $link->delete();
        return Redirect::to('/gtl');
    }

    public function select()
    {
        if (Auth::check()) {
            $id = "all";
            $cname = $id;
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

            if(!empty($data)){
                foreach ($data as $key => $row) {
                    $is_dir[$key]  = $row['is_dir'];
                    $name[$key] = $row['name'];
                }

                array_multisort($is_dir, SORT_DESC, $name, SORT_ASC, $data);
            }

            $parent = $this->getNavbar($cname,"","");
            return view('pages.gtl.components.gtl-board', [
                'data' => $data,
                "cname" => $cname,
                'parent' => $parent,
                'in' => $id,
            ]);
        } else return Redirect::to('/');
    }

    public function selectIn()
    {
        if (Auth::check()) {
            $id = $_GET['connection_name'];
            $any = $_GET['path'];
            $cname = 'all';
            $proObj = new Provider($id);
            $data = $proObj->getFiles($any);

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
            return view('pages.gtl.components.gtl-board',[
                'data' => $data,
                "cname" => $cname,
                'parent' => $parent,
                'in' => $id
            ]);
        } else return Redirect::to('/');
    }

    private function getNavbar($id,$pathname,$path)
    {
        $parent = (object)array(
            'par_now' => "",
            'par_name' => array(),
            'par_path' => array()
        );
        $parent->par_now = $path;
        $pathname = $id.$pathname;
        $path = $id.$path;
        $parent->par_name = explode("/", $pathname);
        $paths = explode("/", $path);
        $temp = '/';
        for ($i = 0; $i < count($parent->par_name); $i++) {
            if ($i == 0) {
                $temp = $temp . $paths[$i];
                $parent->par_path[] = $temp;
                $temp = '/';
            } else {
                $temp = $temp . $paths[$i];
                $parent->par_path[] = $temp;
                $temp = $temp . '/';
            }
        }
        return $parent;
    }
}
