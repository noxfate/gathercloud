<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;
use App\AppModels\Provider;
use Illuminate\Support\Facades\Redirect;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        if (Auth::check())
        {
            if($id == 'all')
            {
                $token = User::find(Auth::user()->id)->tokens;
                $data = array();
                foreach($token as $tk)
                {
                    $proObj = new Provider($tk->connection_name);
                    $temp = $proObj->getFiles();
                    $data = array_merge($data,$temp);
                }
                $par = (object)array(
                    'pname' => array(),
                    'ppath' => array(),
                    'pprovider' => array()
                );
                return view('pages.cloud.index',[
                    'data' => $data,
                    "cname" => "all",
                    'cmail' => 'eiei',
                    'parent' => $par
                ]);
            }
            else
            {

            }
        } else return Redirect::to('/');
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
    public function show($id,$any)
    {

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
