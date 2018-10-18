<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>['show','create','store','index','confirmEmail']
    ]);

        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
    
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        $statuses = $user->statuses()
                         ->orderBy('created_at','desc')
                         ->paginate(30);

    	return view('users.show',compact('user','statuses'));
    }

    public function store(Request $request)
    {
    	
    //验证规则
    	$this->validate($request,[
    		'name'=>'required|max:50',
    		'email'=>'required|email|unique:users|max:255',
    		'password'=>'required|confirmed|min:6'
    	]);

    //存入数据库
    	$user = User::create([
    		'name'=>$request->name,
    		'email'=>$request->email,
    		'password'=>bcrypt($request->password),
    	]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success','已发送到您的邮箱！');
        return redirect('/');

        Auth::login($user);
        session()->flash('success','欢迎');
    	return redirect()->route('users.show',[$user]);
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册！";

        Mail::send($view,$data,function($message) use ($from,$name,$to,$subject){
            $message->from($from,$name)->to($to)->subject($subject);
        });
    }

    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //修改资料
    public function update(User $user,Request $request)
    {
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'required|confirmed|min:6'
        ]);

        $this->authorize('update',$user);

        $data =[];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success','修改成功!');


        return redirect()->route('users.show',$user->id);
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','已删除');
        return back();
    }

    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜你，激活成功！');
        return redirect()->route('users.show',[$user]);
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = "关注的人";
        return view('users.show_follow',compact('users','title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = "粉丝";
        return view('users.show_follow',compact('users','title'));
    }
}