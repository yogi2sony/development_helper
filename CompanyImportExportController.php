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
use App\Models\Importexport_log;
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

class CompanyImportExportController extends Controller
{
    
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        $items = Importexport_log::where('tablename', '=', 'companies')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(10);
        $oldpath = 'uploads/sample/upload-company-sample.xlsx';
        $path = 'sample/upload-company-sample.xlsx';
        return view('admin.companyimportexport',compact('items','path'));
    }

    public function import(Request $request){
        //Excel::import(new ImportCompanies, request()->file('file'));
        //dd('Result: ' . $maxcid);
        $this->validate($request,[
            'file'=>'required|mimes:xls,xlsx',
            //'file'=>'required|in:doc,csv,xlsx,xls,docx,ppt,odt,ods,odp',
        ]);
        if ($request->hasFile('file')){
            $maxcid = Companie::max('id');
            //$file = $request->file('file');
            $file = request()->file('file');
            $import = new ImportCompanies;
            Excel::import($import, $file);
            //=============================
            $rows = $import->getRowCount();
            $items = new Importexport_log;
            $items->name = 'Import Company';
            $items->description = 'Import new company excel file';
            $items->tablename = 'companies';
            $items->totalrows = $rows;
            $items->startrow = $maxcid+1;
            $items->endrow = $maxcid+$rows;
            $size = $file->getSize();
            $type = $file->getClientmimeType();
            $filename = $file->getClientOriginalName();
            $directory = 'public/uploads/ImportExportFiles/';
            $splitName = explode('.', $filename, 2);
            $date = new DateTime("now");
            $strip = $date->format('YmdHis');
            $path = $directory.$splitName[0].$strip.'.'.$splitName[1];
            $storename = $splitName[0].$strip.'.'.$splitName[1];
            $file->storeAs($directory,  $storename ,'local');
            $items->filename = $storename;
            $items->folderpath = $path;
            $items->filesize = $size;
            $items->filetype = $type;
            $items->status = 'Imported Successfully';
            $items->created_by = Auth::user()->id;
            $items->save();
            session()->flash('message','Imported Successfully');
        }else{
            session()->flash('message','Please select a file');
        }
        return back();
    }

    public function export(){
        return Excel::download(new ExportCompanies, 'companies.xlsx');
    }

    public function exportCSV(){
        return Excel::download(new ExportCompanies, 'companies.csv');
    }

    public function searchLead(Request $request){
        if($request->has('q')){
            $search = $request->q;
            $items = View_lead::where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orderBy('created_at', 'desc')->simplePaginate(10);
        }else{
            $items = View_lead::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(10);
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
            $data = Product::where('is_delete', '=', '0')->select("name")->where('name','LIKE',"%$search%")->get();
        }
        return response()->json($data);
    }

    public function dataAjax(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            /*$data = DB::table("view_leads")->select("id","name")->where('name','LIKE',"%$search%")->get();*/
            $data = View_lead::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->get();
        }
        return response()->json($data);
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
