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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('user.show', [$user]);
    }



}
