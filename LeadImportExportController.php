<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Companie;
use App\Models\Lead_stage;
use App\Models\View_lead;
use App\Models\Html_head_tbl;
use App\Models\Todo_comment;
use App\User;
use App\Models\Importexport_log;
use App\Exports\ExportUsers;
use App\Imports\ImportUsers;
use App\Exports\ExportLeads;
use App\Imports\ImportLeads;
use App\Exports\ExportCompanies;
use App\Imports\ImportCompanies;
use App\Exports\ExportLeadReports;
use App\Exports\ExportUserLeadReports;
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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class LeadImportExportController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        $items = Importexport_log::where('tablename', '=', 'leads')->where('type', '=', '2')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(10);
        $oldpath = 'uploads/sample/upload-lead-sample.xlsx';
        $path = 'sample/upload-lead-sample.xlsx';
        return view('admin.leadimportexport',compact('items','path'));
    }

    public function import(Request $request){
        //Excel::import(new ImportLeads, request()->file('file'));
        //dd('Result: ' . $maxcid);
        $this->validate($request,[
            'file'=>'required|mimes:xls,xlsx',
            //'file'=>'required|in:doc,csv,xlsx,xls,docx,ppt,odt,ods,odp',
        ]);
        if ($request->hasFile('file')){
            $maxcid = Lead::max('id');
            //$file = $request->file('file');
            $file = request()->file('file');
            $import = new ImportLeads;
            Excel::import($import, $file);
            //=============================
            $rows = $import->getRowCount();
            $items = new Importexport_log;
            $items->name = 'Import Lead';
            $items->description = 'Import new Lead excel file';
            $items->tablename = 'leads';
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
            $items->type = 2;
            $items->created_by = Auth::user()->id;
            $items->save();
            session()->flash('message','Imported Successfully');
        }else{
            session()->flash('message','Please select a file');
        }
        return back();
    }

    public function export(){
        return Excel::download(new ExportLeads, 'leads.csv');
    }

    public function exportLeadRows(Request $request){
        // file extensions: .csv .xlsx .xls
        //$query = Lead::where('id',$id)->get();
        //dd($query);
        return Excel::download(new ExportLeadReports($request->leadrows_id), 'lead_report.csv');
    }

    public function exportUserLeadRows(Request $request){
        $items = array();
        $items[] = $request->userid;
        $items[] = $request->startdate;
        $items[] = $request->enddate;
        //dd($items);
        return Excel::download(new ExportUserLeadReports($items), 'user_lead_report.csv');
    }

    public function exportCSV(){
        return Excel::download(new ExportLeads, 'companies.csv');
    }

    public function zexportLeadRows($id){          
        $query = Lead::where('id',$id)->get();
        dd($query);
        return Excel::download($query, 'lead_report.xlsx');    
    }

    public function xexportLeadRows(Request $request){
        $data = $request->arraydata;
        return Excel::download(new ExportLeadReports, 'lead_report.xlsx');
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
