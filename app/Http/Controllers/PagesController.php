<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Cache;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Jobs\CreateFileMapping;
use Illuminate\Support\Facades\Redirect;
use App\Library\FileMapping;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.landing');
    }

    public function dropbox()
    {
        $dropbox = new \App\Library\DropboxModel();

        return $dropbox->getFiles();
//        return "<h1>Access Token Not Found Problem</h1>";
    }
//
//    public function copy()
//    {
//        $copy = new \App\Library\CopyModel();
//        if (!isset($_GET['oauth_token'])){
//            return Redirect::to($copy->getRequestToken());
//        }else{
//            return $copy->getAccessToken($_GET['oauth_token']);
//        }
//
//
//    }




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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function about()
    {
        return view('pages.about');
    }

    public function test()
    {
        // $path = 'id/ff';
        // $parent = (object) array(
        //     'pname' => array(),
        //     'ppath' => array()
        // );
        // $parent->pname = explode("/", $path);
        // var_dump($parent);
        // $temp = '/';
        // for($i = 0; $i < count($parent->pname); $i++){
        //     if( $i == 0){
        //     $temp = $temp . $parent->pname[$i];
        //     $parent->ppath[] = $temp;
        //     echo $parent->pname[$i] . " --- " . $parent->ppath[$i] . "<br>";
        //     $temp = '/';
        //     }else{
        //         $temp = $temp . $parent->pname[$i];
        //         $parent->ppath[] = $temp;
        //         echo $parent->pname[$i] . " --- " . $parent->ppath[$i] . "<br>";
        //         $temp = $temp . '/';
        //     }

        // }
        $data = json_decode(Cache::where('user_id',1)->get()->find(2)->data, true);
        // dd($data);
        $ob = new FileMapping($data);
        var_dump($ob->traverseInsideFolder($data, "/Test/aaa/in_aaa/new_folder"));


    }

    public  function context(){
        return view('test.testcontextmenu');
    }
}
