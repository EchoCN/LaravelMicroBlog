<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;


class SessionController extends Controller
{    
    public function __construct()
    {
    	$this->middleware('guest',[
    		'only'=>['create']
    	]);
    }


	public function create()
	{
		return view('sessions.create');
	}

	public function store(Request $request)
	{
		$credentials = $this->validate($request,[
			'email'=>'required|email|max:255',
			'password'=>'required'
		]);

		if(Auth::attempt($credentials,$request->has('remember')))
			{
				if(Auth::user()->activated){
				//登入成功的操作
				session()->flash('success','欢迎回来');
				return redirect()->intended(route('users.show',[Auth::user()]));
			}else{
				Auth::logout();
				session()->flash('warning','你的账户未激活，请通过邮箱激活！');
				return redirect('/');
			}
			}else{
				//失败的操作
				session()->flash('danger','登入失败');
				return redirect()->back();
			}
	}

	public function destroy()
	{
		Auth::logout();
		session()->flash('success','成功退出！');
		return redirect('login');
	}
}
