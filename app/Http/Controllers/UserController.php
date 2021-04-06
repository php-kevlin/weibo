<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',[
           'except' => ['show','create','store']
        ]);

        $this->middleware('guest',[
           'only'=>['create']
        ]);
    }

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

        Auth::login($user);

        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('user.show', [$user]);
    }

    public function edit(User $user)
    {
//        dd();
//        dd($user->id);
        $this->authorize('update',$user);
        return view('user.edit',compact('user'));
    }

    public function update(User $user,Request $request)
    {
        $this->authorize('update',$user);
        $this->validate($request,[
           'name'=>'required',
           'password'=>'nullable'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success',"个人资料更新成功");

        return redirect()->route('user.show',$user);

    }


}
