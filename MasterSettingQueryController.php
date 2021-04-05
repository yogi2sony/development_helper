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

class MasterSettingQueryController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        $title = 'Master Setting';
        return view('admin.mastersetting',compact('title'));
    }

    public function resetLeadRefKey(){
        //$data = lead::get(['id']);
        $data = lead::select('id')->get();
        foreach ($data as $key => $id) {
            $id =$id->id;
            if ($id<10) {
                $result = 'LDSN000'.($id);
            }elseif ($id<100 && $id>=10) {
                $result = 'LDSN00'.($id);
            }elseif ($id<1000 && $id>=100) {
                $result = 'LDSN0'.($id);
            }elseif ($id>=1000) {
                $result = 'LDSN'.($id);
            }

            $item = lead::find($id);
            if ($item->lead_sn != $result) {
                $item->lead_sn = $result;
                $item->save();
            }
            //dd($item);
        };
        //dd('Stop');
        $title = 'Successfully Reset';
        return view('admin.mastersetting',compact('title'));
    }

    /**
    $items = Companie::find($id);
    $items->bank_branch = $request->bank_branch;
    $items->save();

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
