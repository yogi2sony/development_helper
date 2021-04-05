<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Todo_comment;
use App\User;
use Auth;
use DB;
use Mail;
use Sentinel;
use Reminder;
//use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class ForgotPasswordController extends Controller
{
	public function __construct(){
        $this->middleware('auth');
    }

    public function main_fun(){
    	$title = 'Forgot Password';
    	/*$users = User::all();*/
        return view('admin.forgotpassword',compact('title'));
    }

    public function forgot(){
    	$title = 'Forgot Password';
    	return view('admin.forgotpassword',compact('title'));
    }

    public function resetForgotpassword(Request $request){
    	//dd($request->all());
    	$user = User::whereEmail($request->email)->first();
    	if ($user == null) {
    		return redirect()->back()->with(['error' => 'Email Not Exists']);
    	};
    	$user = Sentinel::findById($user->id);
    	$reminder = Reminder::exists($user) ? : Reminder::create($user);
    	$this->sendEmail($user, $reminder->code);
    	return redirect()->back()->with(['success' => 'Reset code send to your email.']);
    }

// 'email.forgot',
    public function sendEmail($user, $code){
    	Mail::send(
    		'sendmail.forgot',
    		['user' => $user, 'code' => $code],
    		function($message) use ($user){
    			$message->to($user->email);
    			$message->subject("$user->name, reset your password");
    		}
    	);
    }
    
}
