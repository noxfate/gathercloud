<?php
/**
 * Created by PhpStorm.
 * User: Arraylist
 * Date: 18-Apr-16
 * Time: 3:12 AM
 */

namespace App\AppModels;

use App\Providers;
use Auth;
use App\User;
use App\Token;
use App\Http\Requests;
use Session;

class AddConnectionService
{

    private $provider_value;
    private $provider_id;

    function __construct($provider_value)
    {
        $this->provider_value = $provider_value;
        $this->provider_id = Providers::where("reference_name",$this->provider_value)->first()->id;
    }

    public function add(){

        $className = '\\App\\Library\\' . $this->provider_value . 'Interface';
        $objInterface = new $className;
        $query = User::find(Auth::user()->id)->token->where("provider",$this->provider_id);
        $connection_email = $objInterface->getAccountInfo()->email;

        $have_connection = false;
        foreach($query as $val){
            if ($connection_email == $val->connection_email){
                $have_connection = true;
                break;
            }
        }

        if($have_connection){
            $tk =  User::find(Auth::user()->id)->token
                ->where('connection_email',$connection_email)
                ->where('provider_id',$this->provider_id)
                ->first();
        } else {
            $gtc_folder = $objInterface->searchFile('GatherCloudForAll');
            if(empty($gtc_folder)){
                $gtc_folder = $objInterface->uploadFile('GatherCloudForAll',null);
            }
            $gtc_folder = $objInterface->normalizeMetaData($gtc_folder,"","");
            $connection_name = Session::get('new_connection_name');
            $tk = new Token();
            $tk->connection_name = $connection_name;
            $tk->connection_email = $connection_email;
            $tk->user_id = Auth::user()->id;
            $tk->provider_id = $this->provider_id;
            $tk->gtc_folder = $gtc_folder[0]['path'];
        }

        $tk->access_token = $objInterface->getToken()->access_token;
        $tk->expired_in = $objInterface->getToken()->expired_in;
        $tk->refresh_token = $objInterface->getToken()->refresh_token;
        $tk->save();
    }

}

//        switch ($this->service) {
//            case "dropbox":
//                $dbxInterface = new \App\Library\DropboxInterface();
//                $token = $dbxInterface->getAccessToken();
//
//                $exist = false;
//                $query = User::find(Auth::user()->id)->tokens->where("provider","dropbox");
//                $conname = Session::get('new_conname');
//                $connEmail = $dbxInterface->getAccountInfo()->email;
//                foreach($query as $val){
//                    if ($connEmail == $val->connection_email){
//                        $exist = true;
//                        break;
//                    }
//                }
//
//                if ($exist)
//                {
//
//                    $tk =  User::find(Auth::user()->id)->tokens
//                        ->where('connection_email',$connEmail)
//                        ->where('provider',$this->service)
//                        ->first();
//                    $tk->access_token = json_encode($token);
//
//                }else{
//                    $tk = new Token();
//
//                    $tk->connection_name = $conname;
//                    $tk->connection_email = $connEmail;
//                    $tk->access_token = json_encode($token);
//                    $tk->access_token_expired = "";
//                    $tk->refresh_token = "";
//                    $tk->refresh_token_expired = "";
//                    $tk->user_id = Auth::user()->id;
//                    $tk->provider = $this->service;
//                }
//
//                $tk->save();
//                break;
//            case "copy":
//                $cpyInterface = new \App\Library\CopyInterface();
//                $token = $cpyInterface->getAccessToken();
//                $exist = false;
//
//                $conname = Session::get('new_conname');
//                $connEmail = \GuzzleHttp\json_decode($cpyInterface->getAccountInfo())->email;
//                $query = User::find(Auth::user()->id)->tokens->where("provider","copy");
//
//                foreach($query as $val){
//                    if ($connEmail == $val->connection_email){
//                        $exist = true;
//                        break;
//                    }
//                }
//
//                if ($exist)
//                {
//
//                    $tk =  User::find(Auth::user()->id)->tokens
//                        ->where('connection_email',$connEmail)
//                        ->where('provider',$this->service)
//                        ->first();
//                    $tk->access_token = json_encode($token);
//
//                }else{
//                    $tk = new Token();
//                    $tk->connection_name = $conname;
//                    $tk->connection_email = $connEmail;
//                    $tk->access_token = json_encode($token);
//                    $tk->access_token_expired = "";
//                    $tk->refresh_token = "";
//                    $tk->refresh_token_expired = "";
//                    $tk->user_id = Auth::user()->id;
//                    $tk->provider = $this->service;
//                }
//
//                $tk->save();
//                break;
//            case "box":
//                $boxInterface = new \App\Library\BoxInterface();
//                $token = $boxInterface->getAccessToken();
//                $userInfo = $boxInterface->getAccountInfo();
//
//                $exist = false;
//
//                $conname = Session::get('new_conname');
//                $connEmail = $userInfo["login"];
//                $query = User::find(Auth::user()->id)->tokens->where("provider","box");
//
//                foreach($query as $val){
//                    if ($connEmail == $val->connection_email){
//                        $exist = true;
//                        break;
//                    }
//                }
//
//                if ($exist)
//                {
//
//                    $tk =  User::find(Auth::user()->id)->tokens
//                        ->where('connection_email',$connEmail)
//                        ->where('provider',$this->service)
//                        ->first();
//                    $tk->access_token = json_encode($token);
//
//                }else{
//                    $tk = new Token();
//                    $tk->connection_name = $conname;
//                    $tk->connection_email = $connEmail;
//                    $tk->access_token = json_encode($token);
//                    $tk->access_token_expired = "";
//                    $tk->refresh_token = "";
//                    $tk->refresh_token_expired = "";
//                    $tk->user_id = Auth::user()->id;
//                    $tk->provider = $this->service;
//                }
//
//                $tk->save();
//                break;
//            case "onedrive":
//                $ondInterface = new \App\Library\OneDriveInterface();
//                $token = $ondInterface->getAccessToken();
//                $userInfo = $ondInterface->getAccountInfo();
//
//                $exist = false;
//
//                $conname = Session::get('new_conname');
//                $connEmail = $userInfo->id;
//                $query = User::find(Auth::user()->id)->tokens->where("provider","onedrive");
//
//                foreach($query as $val){
//                    if ($connEmail == $val->connection_email){
//                        $exist = true;
//                        break;
//                    }
//                }
//
//                if ($exist)
//                {
//                    $tk =  User::find(Auth::user()->id)->tokens
//                        ->where('connection_email',$connEmail)
//                        ->where('provider',$this->service)
//                        ->first();
//                    $tk->access_token = json_encode($token);
//
//                }else{
//                    $tk = new Token();
//                    $tk->connection_name = $conname;
//                    $tk->connection_email = $connEmail;
//                    $tk->access_token = json_encode($token);
//                    $tk->access_token_expired = "";
//                    $tk->refresh_token = "";
//                    $tk->refresh_token_expired = "";
//                    $tk->user_id = Auth::user()->id;
//                    $tk->provider = $this->service;
//                }
//
//                $tk->save();
//                break;
//            case "googledrive":
//                $ggInterface = new \App\Library\GoogleInterface();
//                $token = $ggInterface->getAccessToken();
//                $re_token = $ggInterface->getRefreshToken();
//                $userInfo = $ggInterface->getAccountInfo();
//
//                $exist = false;
//
//                $conname = Session::get('new_conname');
//                $connEmail = $userInfo->getPermissionId();
//                $query = User::find(Auth::user()->id)->tokens->where("provider","googledrive");
//
//                foreach($query as $val){
//                    if ($connEmail == $val->connection_email){
//                        $exist = true;
//                        break;
//                    }
//                }
//
//                if ($exist)
//                {
//                    $tk =  User::find(Auth::user()->id)->tokens
//                        ->where('connection_email',$connEmail)
//                        ->where('provider',$this->service)
//                        ->first();
//                    $tk->access_token = json_encode($token);
//
//                }else{
//                    $tk = new Token();
//                    $tk->connection_name = $conname;
//                    $tk->connection_email = $connEmail;
//                    $tk->access_token = json_encode($token);
//                    $tk->access_token_expired = "";
//                    $tk->refresh_token = json_encode($re_token);
//                    $tk->refresh_token_expired = "";
//                    $tk->user_id = Auth::user()->id;
//                    $tk->provider = $this->service;
//                }
//
//                $tk->save();
//                break;
//            default:
//                return "Error!! Provider: $this->provider";
//        }
