<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Token;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class CloudController extends Controller
{

    private $dbxModel;
    private $cpyModel;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check())
            return view('pages.addcloud');
        return Redirect::to('/home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }


    public function add($service)
    {
        if ($service == "dropbox"){

            $this->dbxModel = new \App\Library\DropboxModel();
            $token = $this->dbxModel->getToken();

            $exist = false;
            $query = User::find(Auth::user()->id)->tokens->where("provider","dropbox");
            $connEmail = $this->dbxModel->getAccountInfo()->email;
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
//                if (empyty($_POST['conname']))
//                    $tk->connection_name = $connEmail;
//                else
//                    $tk->connection_name = $_POST['conname'];
                $tk->connection_name = $connEmail;
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

        if ($service == "copy"){
            $this->cpyModel = new \App\Library\CopyModel();
            $token = $this->cpyModel->getAccessToken();

            $exist = false;
            $connEmail = \GuzzleHttp\json_decode($this->cpyModel->getAccountInfo())->email;
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
//                if (empyty($_POST['conname']))
//                    $tk->connection_name = $connEmail;
//                else
//                    $tk->connection_name = $_POST['conname'];
                $tk->connection_name = $connEmail;
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

        if ($service == "box"){
            $boxModel = new \App\Library\BoxModel();
            print_r($boxModel->getAccessToken());
            print_r($boxModel->getRefreshToken());
            $userInfo = $boxModel->getAccountInfo();
            $originalEmail = $userInfo["login"];

        }

        if ($service == "onedrive"){
            $ondModel = new \App\Library\OneDriveModel();
            print_r($ondModel->getAccessToken());
//            print_r($ondModel->getRefreshToken());

        }

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
        //
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
}
