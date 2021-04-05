<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\User;
use DB;
use Auth;
use Redirect;
use DateTime;

class AdminLeadPortalController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        if ($siurole == 1) {
        	$items = Lead::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(20);
        } else {
            return view('errors.403');
        };
        $title  = 'Admin Portal For Lead';
        return view('admin.lead.administrator',compact('items','title'));
    }
}
