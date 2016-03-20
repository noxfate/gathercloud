<?php

namespace App\Http\Controllers;

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
            return view('pages.cloud-setting',[
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
            return view('pages.addcloud');
        return Redirect::to('/');
    }


    public function add($service)
    {

        if (!empty($_REQUEST['conname'])){
            Session::put('new_conname', $_REQUEST['conname']);
            Session::save();

        }
        if ($service == "dropbox"){

            $dbxInterface = new \App\Library\DropboxInterface();
            $token = $dbxInterface->getToken();

            $exist = false;
            $query = User::find(Auth::user()->id)->tokens->where("provider","dropbox");
            $conname = Session::get('new_conname');
            $connEmail = $dbxInterface->getAccountInfo()->email;
            foreach($query as $val){
                if ($connEmail == $val->connection_email){
                    $exist = true;
                    break;
                }
            }

            if ($exist)
            {

                $tk =  User::find(Auth::user()->id)->tokens
                    ->where('connection_email',$connEmail)
                    ->where('provider',$service)
                    ->first();
                $tk->access_token = json_encode($token);

            }else{
                $tk = new Token();

                $tk->connection_name = $conname;
                $tk->connection_email = $connEmail;
                $tk->access_token = json_encode($token);
                $tk->access_token_expired = "";
                $tk->refresh_token = "";
                $tk->refresh_token_expired = "";
                $tk->user_id = Auth::user()->id;
                $tk->provider = $service;
            }

            $tk->save();
        }
        elseif ($service == "copy"){
            $cpyInterface = new \App\Library\CopyInterface();
            $token = $cpyInterface->getAccessToken();
            $exist = false;

            $conname = Session::get('new_conname');
            $connEmail = \GuzzleHttp\json_decode($cpyInterface->getAccountInfo())->email;
            $query = User::find(Auth::user()->id)->tokens->where("provider","copy");

            foreach($query as $val){
                if ($connEmail == $val->connection_email){
                    $exist = true;
                    break;
                }
            }

            if ($exist)
            {

                $tk =  User::find(Auth::user()->id)->tokens
                    ->where('connection_email',$connEmail)
                    ->where('provider',$service)
                    ->first();
                $tk->access_token = json_encode($token);

            }else{
                $tk = new Token();
                $tk->connection_name = $conname;
                $tk->connection_email = $connEmail;
                $tk->access_token = json_encode($token);
                $tk->access_token_expired = "";
                $tk->refresh_token = "";
                $tk->refresh_token_expired = "";
                $tk->user_id = Auth::user()->id;
                $tk->provider = $service;
            }

            $tk->save();


        }
        elseif ($service == "box"){
            $boxInterface = new \App\Library\BoxInterface();
            $token = $boxInterface->getAccessToken();
            $userInfo = $boxInterface->getAccountInfo();

            $exist = false;

            $conname = Session::get('new_conname');
            $connEmail = $userInfo["login"];
            $query = User::find(Auth::user()->id)->tokens->where("provider","box");

            foreach($query as $val){
                if ($connEmail == $val->connection_email){
                    $exist = true;
                    break;
                }
            }

            if ($exist)
            {

                $tk =  User::find(Auth::user()->id)->tokens
                    ->where('connection_email',$connEmail)
                    ->where('provider',$service)
                    ->first();
                $tk->access_token = json_encode($token);

            }else{
                $tk = new Token();
                $tk->connection_name = $conname;
                $tk->connection_email = $connEmail;
                $tk->access_token = json_encode($token);
                $tk->access_token_expired = "";
                $tk->refresh_token = "";
                $tk->refresh_token_expired = "";
                $tk->user_id = Auth::user()->id;
                $tk->provider = $service;
            }

            $tk->save();

        }
        elseif ($service == "onedrive"){
            $ondInterface = new \App\Library\OneDriveInterface();
            $token = $ondInterface->getAccessToken();
            $userInfo = $ondInterface->getAccountInfo();

            $exist = false;

            $conname = Session::get('new_conname');
            $connEmail = $userInfo->id;
            $query = User::find(Auth::user()->id)->tokens->where("provider","onedrive");

            foreach($query as $val){
                if ($connEmail == $val->connection_email){
                    $exist = true;
                    break;
                }
            }

            if ($exist)
            {

                $tk =  User::find(Auth::user()->id)->tokens
                    ->where('connection_email',$connEmail)
                    ->where('provider',$service)
                    ->first();
                $tk->access_token = json_encode($token);

            }else{
                $tk = new Token();
                $tk->connection_name = $conname;
                $tk->connection_email = $connEmail;
                $tk->access_token = json_encode($token);
                $tk->access_token_expired = "";
                $tk->refresh_token = "";
                $tk->refresh_token_expired = "";
                $tk->user_id = Auth::user()->id;
                $tk->provider = $service;
            }

            $tk->save();

        }
//
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
        $file = $request->file('file');

        // ============== Redundancy Check! ==========================
        if ($request->hasFile('file') && $request->file('file')->isvalid()){
            // return "Last modified: ".date("F d Y H:i:s.",filemtime($_FILES['file']['name']));

            // .. 1. Search the similar FileName
            $que = Cache::where('user_id', Auth::user()->id)->get();

            $data = array();
            foreach ($que as $d ) {
                $inside_json = json_decode($d->data, true);
                foreach ($inside_json as $in){
                    array_push($data, $in);
                }   
            }
            $fmp = new FileMapping();
            $result = array();
            $result = $fmp->searchFiles($data, $_FILES['file']['name'], $result);
            if (empty($result)){
                return "No file with the same name";
            }else{
                // .. 2. Search in $result for similar Size and File Type
                $same_file = array();
                foreach ($result as $d){
                    if (($d['bytes'] == $_FILES['file']['size'])
                        && ($d['mime_type'] == $_FILES['file']['type'])){
                        array_push($same_file, $d);
                    }
                }
                if (empty($same_file)){
                    return "No file Matching. Ok to upload!";
                }else{
                    return $same_file;
                }

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
        $connName = $token->connection_name;
        $token->delete();

        $cac = User::find(Auth::user()->id)->caches->where("user_connection_name", $connName)->first();
        $cac->delete();
        return "Delete!";
    }
}
