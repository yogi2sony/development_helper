<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Models\Todo_comment;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Companie;
use App\Models\Lead_stage;
use App\Models\View_lead;
use App\Models\Html_head_tbl;
use App\User;
use App\Exports\ExportUsers;
use App\Imports\ImportUsers;
use App\Exports\ExportLeads;
use App\Imports\ImportLeads;
use App\Exports\ExportCompanies;
use App\Imports\ImportCompanies;
use Mail;
//use App\Mail\UserRequest;
use Auth;
use Carbon\Carbon;
//use Input;
use Browser;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DemoqueryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     id, name, lead_sn, assign_id, assign_at, assign, discount_amount, advance_amount, total_amount, meeting_at, meeting_message, file_url, source_id, source, stage_id, stage, status_id, status, next_activity_id, next_activity, next_activity_message, next_activity_at, authority_id, authority, company_id, company, contact_person_id, contact_person, contact_person_mobile, converted_into, converted_id, description, is_delete, created_by, created_at, updated_at
     */
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        //$items = View_lead::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $items = View_lead::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->paginate(10);
        /* simplePaginate paginate(10) */
        $users = User::all();
        $lead_stages = Lead_stage::where('is_delete', '=', '0')->where('id', '=', 2)->get();
        $html_head_tbls = Html_head_tbl::all();
        //dd($lead_stages[0]->name);
        $itemarray = array('lead_sn', 'name', 'assign_id', 'assign_at', 'assign', 'discount_amount', 'advance_amount', 'total_amount', 'meeting_at', 'meeting_message');
        return view('admin.demoarray',compact('items','lead_stages','users','itemarray','html_head_tbls'));
    }


    public function import(){
        Excel::import(new ImportCompanies, request()->file('file'));
        session()->flash('message','Created Successfully');
        return back();
    }

    public function export(){
        return Excel::download(new ExportCompanies, 'companies.xlsx');
    }

    public function exportUsers(){
        return Excel::download(new ExportUsers, 'users.csv');
    }

    public function exportCSV(){
        return Excel::download(new ExportCompanies, 'companies.csv');
    }

    public function searchLead(Request $request){
        if($request->has('q')){
            $search = $request->q;
            $items = View_lead::where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orderBy('created_at', 'desc')->paginate(10);
        }else{
            $items = View_lead::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->paginate(10);
        }
        $users = User::all();
        $lead_stages = Lead_stage::where('is_delete', '=', '0')->where('id', '=', 2)->get();
        $html_head_tbls = Html_head_tbl::all();
        $itemarray = array('lead_sn', 'name', 'assign_id', 'assign_at', 'assign', 'discount_amount', 'advance_amount', 'total_amount', 'meeting_at', 'meeting_message');
        return view('admin.demoarray',compact('items','lead_stages','users','itemarray','html_head_tbls'));
    }

    public function ajaxSearchLead(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = View_lead::where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orderBy('created_at', 'desc')->get();
        }
        return response()->json($data);
    }

    public function dataAjaxProduct(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = User::where('is_delete', '=', '0')->select("name")->where('name','LIKE',"%$search%")->get();
        }else{
            $data = User::where('is_delete', '=', '0')->select("name")->take(10)->get();
        }
        return response()->json($data);
    }

    public function dataAjax(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = User::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->get();
        }else{
            $data = User::where('is_delete', '=', '0')->select("id","name")->take(10)->get();
        }
        return response()->json($data);
    }

    public function postdataAjax(Request $request){
        $data = $request->sname;
        return $data;
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
