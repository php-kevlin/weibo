<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',[
           'except' => ['show','create','store','index','confirmEmail']
        ]);

        $this->middleware('guest',[
           'only'=>['create']
        ]);

        // 限流 一个小时内只能提交 10 次请求；
        $this->middleware('throttle:10,60', [
            'only' => ['store']
        ]);
    }

    public function index()
    {
        $users = User::paginate(6);
        return view('user.index',compact('users'));
    }

    //
    public function create()
    {
        return view('user.create');
    }

    public function show(User $user)
    {
        $statuses = $user->statuses()
                    ->orderBy('created_at','desc')
                    ->paginate(10);

        return view('user.show',compact(['user','statuses']));
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

//        Auth::login($user);
        $this->sendEmailConfirmationTo($user);
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

    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success',"成功删除用户");
        return back();
    }

    public function sendEmailConfirmationTo($user)
    {
        $view = "emails.confirm";
        $data = compact('user');
        $from = "zk4012@foxmail.com";
        $name = "zk";
        $to   = $user->email;
        $subject = "感谢注册weibo应用，请确认你的邮箱";

        Mail::send($view,$data,function ($message) use ($from,$name,$to,$subject){
            $message->from($from,$name)->to($to)->subject($subject);
        });

    }

    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success',"恭喜你激活成功!");
        return redirect()->route('user.show',[$user]);

    }






}
