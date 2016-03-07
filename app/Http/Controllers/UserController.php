<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
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
            return view('pages.profile-setting')->with('user',$user);
        }
        return Redirect::to('/');
    }

    public function login()
    {
        if (!Auth::check())
            return view('pages.login');
        return Redirect::to('/home');
    }

    // Login
    public function authenticate()
    {
        if (Auth::attempt(['email' => $_POST["email"], 'password' => $_POST['pwd']]))
        {
            return Redirect::to('/home');
        }else{
            return "Failed";
        }
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return Redirect::to('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.register");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new \App\User();
        $user->email = $_POST['email'];
        $user->first_name = $_POST['fname'];
        $user->last_name = $_POST['lname'];
        if ($_POST['pwd'] == $_POST['repwd'])
            $user->password = Hash::make($_POST['pwd']);
        else
            return "Password Missmatch";
        $user->save();
        return Redirect::to('/login');

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
        $usr = User::find($id);
        if ($request->has('menu') and ($request->input('menu') == "pwd")){
            $req = $request->all();
            if (Hash::check($req['old_pwd'], $usr->password) and ($req['new_pwd'] == $req['re_pwd'])){
                $usr->password = Hash::make($req['new_pwd']);
                $usr->save();
//                return "Success";
            }
            // debug for Another fail cases
        }elseif($request->has('menu') and ($request->input('menu') == "prof")){
            $req = $request->all();
            $usr->first_name = $req['fname'];
            $usr->last_name = $req['lname'];
            $usr->save();
//            return "Success";
        }

        return Redirect::to('/setting/profile');
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
