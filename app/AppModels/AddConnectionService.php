<?php
/**
 * Created by PhpStorm.
 * User: Arraylist
 * Date: 18-Apr-16
 * Time: 3:12 AM
 */

namespace App\AppModels;

use Auth;
use App\User;
use App\Token;
use App\Http\Requests;
use Session;

class AddConnectionService
{

    private $service;


    function __construct($service)
    {
        $this->service = $service;
    }

    public function add(){
        if ($this->service == "dropbox"){

            $dbxInterface = new \App\Library\DropboxInterface();
            $token = $dbxInterface->getAccessToken();

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
                    ->where('provider',$this->service)
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
                $tk->provider = $this->service;
            }

            $tk->save();
        }
        elseif ($this->service == "copy"){
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
                    ->where('provider',$this->service)
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
                $tk->provider = $this->service;
            }

            $tk->save();


        }
        elseif ($this->service == "box"){
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
                    ->where('provider',$this->service)
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
                $tk->provider = $this->service;
            }

            $tk->save();

        }
        elseif ($this->service == "onedrive"){
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
                    ->where('provider',$this->service)
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
                $tk->provider = $this->service;
            }

            $tk->save();

        }elseif ($this->service == "googledrive"){
            $ggInterface = new \App\Library\GoogleInterface();
        }
    }

}