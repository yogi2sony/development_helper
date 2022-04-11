<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Invoice;
use App\Models\Invoice_item;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Companie;
use App\Models\Term;
use App\Models\Todo_comment;
use Auth;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;
use App\Http\Controllers\Controller;
use PDF;
use Elibyy\TCPDF\Facades\TCPDF;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title='Lead System';
        return redirect('/dashboard');
        
        //return view('website.index',compact('title'));
        /*return view('home');*/
    }
}
