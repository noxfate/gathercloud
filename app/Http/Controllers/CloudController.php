<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
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
        return view('pages.addcloud');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function add($service)
    {
        if ($service == "dropbox"){

            $dbxModel = new \App\Library\DropboxModel();
            $token = $dbxModel->getToken();

            echo "Hello Test";

            $chk = \App\User::find(Auth::user()->id)->tokens->first()->connection_email;
            $originalEmail = $dbxModel->getAccountInfo()->email;
            echo "Hello Test";
            if ($originalEmail == $chk)
            {
                echo "Hello Test";
                $tk =  \App\User::find(Auth::user()->id)->tokens->first();
                $tk->access_token = json_encode($token);
                echo "Hello Test";
            }else{
                $tk = new \App\Token();
                $tk->connection_name = "";
                $tk->connection_email = $dbxModel->getAccountInfo()->email;
                $tk->access_token = json_encode($token);
                $tk->access_token_expired = "";
                $tk->refresh_token = "";
                $tk->refresh_token_expired = "";
                $tk->user_id = Auth::user()->id;
                $tk->provider = $service;
            }

            $tk->save();
//            return Redirect::to('/add');
        }

        if ($service == "copy"){

        }

        if ($service == "box"){
            $bla = new \App\Library\BoxModel();
            print_r($bla->getAccessToken());

            print_r($bla->getRefreshToken());

        }

        if ($service == "onedrive"){

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
