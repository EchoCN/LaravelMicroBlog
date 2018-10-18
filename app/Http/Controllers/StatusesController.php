<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;
use Auth;

class StatusesController extends Controller
{
    public function __consrtuct()
    {
    	$this->middleware('auth');
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
    		'content'=>'required|max:140'
    	]);

    	Auth::user()->statuses()->create([
    		'content'=>$request['content']
    	]);
    	return redirect()->back();
    }

    public function destroy(Status $status)
    {
        $this->authorize('destroy',$status);
        $status->delete();
        session()->flash('success','删除成功！');
        return redirect()->back();
    }
}
