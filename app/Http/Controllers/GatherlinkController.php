<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Token;
use App\Cache;
use App\Link;
use App\Jobs\CreateFileMapping;
use App\Library\FileMapping;
use App\Http\Requests;
use App\Http\Controllers\Controller;
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
        return view('pages.gtl.gtl-landing');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (empty($_GET['selected-item'])){
            return view('pages.gtl.gtl-create');
        }
        else{
            $que = Cache::where('user_id',Auth::user()->id)->get();

            $data = array();
            foreach ($que as $d ) {
                $inside_json = json_decode($d->data, true);
                foreach ($inside_json as $in){
                    array_push($data, $in);
                }   
            }
            $selected = json_decode($_GET['selected-item']);
            $response_arr = array();
            foreach ($selected as $key) {
                array_push($response_arr, $data[$key]);
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
        $link = new Link();
        $link->link_name = $request->input('lkname');
        $link->user_id = Auth::user()->id;
        $link->url = substr(base64_encode(sha1(mt_rand())), 0, 16);
        $link->data = $request->input('items');
        $link->save();
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
            $link = Link::find($id);
            return view("pages.gtl.gtl-index")->with('link', $link);
        }
        return Redirect::to('/');
    }

    public function showFromToken()
    {
        $link = Link::where("url",$_GET['tokens'])->get();
        return $link;
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
        if(Link::find($id)->delete()){
            return Redirect::to('/home');
        }
    }
}
