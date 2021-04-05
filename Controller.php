<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\User;
use App\Models\Todo_comment;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $todorows;

   	public function __construct(){
   		//dd('BaseController');
	    $this->middleware(function ($request, $next) {
			//$this->user= Auth::user();
			$todorows = Todo_comment::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
			$author_name ='Lead Developer:- Yogesh Soni';
			//View::share('users'=> $users, 'username'=> $username);
			//return $next($request);
			//return view()->with(['users' =>$users, 'username' =>$username]);
			//dd('BaseController');
			View::share(compact(['todorows', $todorows], ['author_name', $author_name]));
			return $next($request);
	   });
	   // View::share('users', $users);
   
   }
}
