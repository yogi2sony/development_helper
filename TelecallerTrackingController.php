<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead_telecaller_log;
use App\Models\View_lead_telecaller_log;
use App\Models\Companie;
use App\Models\View_lead;
use DB;
use App\User;
use Auth;
use DateTime;

class TelecallerTrackingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        if ($siurole == 1) {
            /*$items = Lead_telecaller_log::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(20);*/
            $items = View_lead_telecaller_log::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(50);
        } else {
            $items = View_lead_telecaller_log::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->orderBy('created_at', 'desc')->simplePaginate(50);
            /*return view('errors.401'); assign_id */
        };
        $title = 'Manage Telecaller Tracking';
        return view('admin.telecallertracking.index',compact('items','title'));
    }

    public function getDashboard(){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        if ($siurole == 1) {
            $totcalls = View_lead_telecaller_log::where('is_delete', '=', '0')->count('id');
            $attendcalls = View_lead_telecaller_log::where('call_status_id', '=', '1')->where('is_delete', '=', '0')->count('id');
            $restcalls = View_lead_telecaller_log::where('call_status_id', '!=', '1')->where('is_delete', '=', '0')->count('id');
            $items = View_lead_telecaller_log::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        } else {
            $totcalls = View_lead_telecaller_log::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->count('id');
            $attendcalls = View_lead_telecaller_log::where('call_status_id', '=', '1')->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->count('id');
            $restcalls = View_lead_telecaller_log::where('call_status_id', '!=', '1')->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->count('id');
            $items = View_lead_telecaller_log::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->orderBy('created_at', 'desc')->get();
            /*return view('errors.401');*/
        };
        $title = 'Telecaller Tracking Dashboard';
        return view('admin.telecallertracking.dashboard',compact('items','title','totcalls','attendcalls','restcalls'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
