<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Providers;
use App\File;
use App\Link;
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
        if (empty($_GET['selected-item'])){
            return view('pages.gtl.components.gtl-create');
        }
        else{
            $selected = json_decode($_GET['selected-item']);
            $response_arr = collect();
            foreach ($selected as $key) {
                $file = File::find($key);
                $response_arr = $response_arr->push($file);
            }
            return $response_arr;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $selected = json_decode($request->input('items'));
        $tmp_url = substr(base64_encode(sha1(mt_rand())), 0, 16);
        $link = new Link();
        $link->link_name = $request->input('lkname');
        $link->user_id = Auth::user()->id;
        $link->url = $tmp_url;
//            $link->file_id = $s;
//        dd($link->data);
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

        foreach ($selected as $s){
            $tokenName = substr(dirname($s),1);
            $path = trim(substr($s, strlen($tokenName)+1));
            $provObj = new Provider($tokenName);
            $before_dir = str_replace(basename($path), "", $path);
            $file = $provObj->getFiles($before_dir);
            $key = array_search($path, array_column($file, 'path'));
//            dd($file[$key]);
            $root->children()->create([
                'name' => $file[$key]['name'],
                'path' => $file[$key]['path'],
                'bytes' => $file[$key]['bytes'],
                'size' => $file[$key]['size'],
                'mime_type' => $file[$key]['mime_type'],
                'is_dir' => $file[$key]['is_dir'],
                'shared' => $file[$key]['shared'],
                'modified' => $file[$key]['modified'],
                'token_id' => $provObj->getTokenId(),
                'link_id'=> $lid
            ]);
        }
        return Redirect::to('/home');
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
            return view("pages.gtl.gtl-index",[
                "link" => $link,
                "data" => $data
            ]);

        }
        return Redirect::to('/');
    }

    public function showFromToken()
    {
        $link = Link::where("url",$_GET['tokens'])->first();
        $root = File::roots()->where('link_id', $link->id)->first();
        $data = $root->getDescendants();
        dd($data);
        return $data;
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
        $link = Link::where('link_name',$id)->where('user_id', Auth::user()->id)->get();
        foreach($link as $d){
            $d->delete();
        }
        return "Delete!";
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
                $data = array_merge($data, $temp);
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
}
