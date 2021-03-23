<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function create()
    {
        return view('user.create');
    }

    public function show(User $user)
    {
        return view('user.show',compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
           'name'=>'required|unique:users',
           'email' => 'required|email|unique:users|max:255',
           'password' => 'required|confirmed|min:6'
        ]);
        return ;
    }



}
