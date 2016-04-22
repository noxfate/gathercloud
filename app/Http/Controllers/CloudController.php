<?php

namespace App\Http\Controllers;

use App\AppModels\AddConnectionService;
use App\AppModels\Provider;
use App\File;
use App\Jobs\CreateFileMapping;
use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Token;
use App\Cache;
use App\Library\FileMapping;
use App\Http\Requests;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;


class CloudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()){
            $user = User::find(Auth::user()->id);
            $conn = $user->tokens;
            return view('pages.setting.setting-cloud',[
                "conn" => $conn,
                "user" => $user
            ]);
        }
        return Redirect::to('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check())
            return view('pages.cloud.addcloud');
        return Redirect::to('/');
    }


    public function add($service)
    {

        if (!empty($_REQUEST['connection_name'])){
            Session::put('new_connection_name', $_REQUEST['connection_name']);
            Session::save();

        }

        $addConObj = new AddConnectionService($service);
        $addConObj->add();

        // After Saving Connection, Create a FileMapping Job immediately
//        $job = (new CreateFileMapping(Session::get('new_conname')));
//        $this->dispatch($job);

        return Redirect::to('/add');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $file = $request->file('file');
        // ============== Redundancy Check! ==========================
        if ($request->hasFile('file') && $request->file('file')->isvalid()){
            // .. 1. Search the same FileName, size, mime_type
            // .. 2. Search in $result for similar Size and File Type
            $tk = User::find(Auth::user()->id)->tokens;
            $result = collect();
            foreach($tk as $t){
                $files = $t->files;
                $search = $files->where('name', $_FILES['file']['name'])
                    ->where('bytes', $_FILES['file']['size'])
                    ->where('mime_type', $_FILES['file']['type']);
                $result = $result->merge($search);
            }
            if ($result->count() == 0){

                // ============== Priorities Upload! ==========================
                $priority = array();
                $space = array();
                foreach ($tk as $t){
                    $prov = new Provider($t->connection_name);
                    $space += [ $t->connection_name => $prov->getStorage()['remain']];
                }
                dd($space);

                // ============================================================

            }else{
                return $result;
            }
        // ============================================================




        }
        return "Error";
    }

    //


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
        $token = User::find(Auth::user()->id)->tokens->find($id);
        $old_conn = $token->connection_name;
        $token->connection_name = $request->input('rename');
        $token->save();

        $cac = User::find(Auth::user()->id)->caches->where("user_connection_name", $old_conn)->first();
        $cac->user_connection_name = $request->input('rename');
        $cac->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $token = User::find(Auth::user()->id)->tokens->find($id);
        $connId = $token->id;
        $token->delete();

//        $root = File::roots()->where('token_id',$connId)->first();
//        $root->delete();

        return "Delete!";
    }
}
