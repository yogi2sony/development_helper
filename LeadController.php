<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Lead;
use App\Models\Lead_stage;
use App\Models\Lead_stage_log;
use App\Models\Lead_next_activitie;
use App\Models\Lead_next_activity_log;
use App\Models\Lead_next_activity_message;
use App\Models\Lead_meeting_log;
use App\Models\Lead_contact_person;
use App\Models\Lead_account_transaction;
use App\Models\Lead_converted_module;
use App\Models\Lead_source;
use App\Models\Lead_status;
use App\Models\Lead_assigned_member;
use App\Models\Lead_log;
use App\Models\Lead_comment;
use App\Models\Lead_telecaller_log;
use App\Models\View_lead_telecaller_log;
use App\Models\Invoice;
use App\Models\Invoice_item;
use App\Models\Product;
use App\Models\Companie;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Term;
use App\Models\View_lead;
use App\Models\Event_action_log;
use App\Models\Mail_action_log;
use App\Mail\Taskmail;
use App\Mail\TaskReassignmail;
use App\Models\Todo_comment;
use App\Models\Lead_column;
use App\User;
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
//use Illuminate\Support\Facades\Storage;


class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /*id, name, lead_sn, assign_id, assign_at, assign, discount_amount, advance_amount, total_amount, meeting_at, meeting_message, file_url, source_id, source, stage_id, stage, status_id, status, next_activity_id, next_activity, next_activity_message, next_activity_at, authority_id, authority, company_id, company, company_mobile, company_mobile1, company_mobile2, company_email, company_cpname, company_cpmobile, company_cpemail, company_cprole, contact_person_id, contact_person, contact_person_mobile, converted_into, converted_id, description, is_delete, created_by, created_at, updated_at*/

    public function index(){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $lead_columns = Lead_column::select('id','name','namefield')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $itemarray = Lead_column::where('is_active', '=', '1')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->pluck('namefield')->toArray(); /*->lists('namefield')->toArray();*/
        //$itemarray = array('id','name','company','company_mobile','company_email','company_cpname','company_cpmobile','company_cpemail','company_cprole','contact_person_mobile','next_activity_id','next_activity','next_activity_message','authority','assign','status_id','status');
        //dd($itemarray);
        if ($siurole == 1) {
            $items = View_lead::select($itemarray)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(100);
            $countlead = Lead::where('is_delete', '=', '0')->count();
        } else {
            $items = View_lead::select($itemarray)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(100);
            $countlead = Lead::where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->count();
        };
        if ($countlead) {
            $result = 'Total records found: '.$countlead;
        }else{
            $result = 'Record doesn\'t exist';
        };
        $title = 'All';
        $viewid = 1;
        return view('admin.lead.index',compact('items','title','viewid','countlead','result','lead_columns'));
    }

    public function searchLeaddata(Request $request){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $lead_columns = Lead_column::select('id','name','namefield')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $itemarray = Lead_column::where('is_active', '=', '1')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->pluck('namefield')->toArray();
        //$itemarray = array('id','name','company','company_mobile','company_email','company_cpname','company_cpmobile','company_cpemail','company_cprole','contact_person_mobile','next_activity_id','next_activity','next_activity_message','authority','assign','status_id','status');
        if($request->has('q')){
            $search = $request->q;
            if ($siurole == 1) {
                $items = View_lead::select($itemarray)->where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orwhere('company','LIKE',"%$search%")->orwhere('contact_person_mobile','LIKE',"%$search%")->orwhere('assign','LIKE',"%$search%")->orderBy('created_at', 'desc')->simplePaginate(100);
            } else {
                $field = ['name','company','contact_person_mobile'];
                $items = View_lead::select($itemarray)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->Where(function ($query) use($search, $field) {
                             for ($i = 0; $i < count($field); $i++){
                                $query->orwhere($field[$i], 'like',  '%' . $search .'%');
                             }      
                        })->simplePaginate(100);
                /*$items = View_lead::select($itemarray)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orwhere('company','LIKE',"%$search%")->orwhere('contact_person_mobile','LIKE',"%$search%")->orderBy('created_at', 'desc')->simplePaginate(20);*/
            }
            $items->appends(['q' => $search]);   
        }else{
            if ($siurole == 1) {
                $items = View_lead::select($itemarray)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(100);
            } else {
                $items = View_lead::select($itemarray)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(100);
            }
        };
        $countlead = $items->count();
        if ($countlead) {
            $result = 'Total records found: '.$countlead;
        }else{
            $result = 'Record doesn\'t exist';
        };
        $title = 'Search Result <i>"'.$request->q.'"</i> of ';
        $viewid = 1;
        return view('admin.lead.index',compact('items','title','viewid','countlead','result','lead_columns'));
    }
    
    public function testSearchLeaddata(Request $request){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $lead_columns = Lead_column::select('id','name','namefield')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $itemarray = Lead_column::where('is_active', '=', '1')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->pluck('namefield')->toArray();
        //$itemarray = array('id','name','company','company_mobile','company_email','company_cpname','company_cpmobile','company_cpemail','company_cprole','contact_person_mobile','next_activity_id','next_activity','next_activity_message','authority','assign','status_id','status');
        if($request->has('q')){
            $search = $request->q;
            if ($siurole == 1) {
                $items = View_lead::select($itemarray)->where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orwhere('company','LIKE',"%$search%")->orwhere('contact_person_mobile','LIKE',"%$search%")->orwhere('assign','LIKE',"%$search%")->orderBy('created_at', 'desc')->simplePaginate(20);
            } else {
                /*$items = View_lead::select($itemarray)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orwhere('company','LIKE',"%$search%")->orwhere('contact_person_mobile','LIKE',"%$search%")->orderBy('created_at', 'desc')->simplePaginate(20);*/
                $field = ['name','company','contact_person_mobile'];
                $items = View_lead::select($itemarray)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->Where(function ($query) use($search, $field) {
                             for ($i = 0; $i < count($field); $i++){
                                $query->orwhere($field[$i], 'like',  '%' . $search .'%');
                             }      
                        })->simplePaginate(20);
            }
            $items->appends(['q' => $search]);   
        }else{
            if ($siurole == 1) {
                $items = View_lead::select($itemarray)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(20);
            } else {
                $items = View_lead::select($itemarray)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(20);
            }
        };
        $countlead = $items->count();
        if ($countlead) {
            $result = 'Total records found: '.$countlead;
        }else{
            $result = 'Record doesn\'t exist';
        };
        $title = 'Search Result <i>"'.$request->q.'"</i> of ';
        $viewid = 1;
        dd($items);
        /*return view('admin.lead.index',compact('items','title','viewid','countlead','result','lead_columns'));*/
    }

    public function massEditLead(Request $request){
        $leadarray = $request->leads_id;
        $leadarray = explode(",",$leadarray[0]);
        $leadlength = count($leadarray);
        for($i=0; $i < $leadlength; $i++){ 
            $id = $leadarray[$i];
            $items = lead::find($id);
            if($request->source != ""){
                $items->source = $request->source;
            };
            $on_status = $request->status;
            if($on_status != ""){
                $items->status = $on_status;
            };
            if ($on_status == '2' || $on_status == '5' || $on_status == '24') {
                $items->stage = '3';
            }else {
                $items->stage = '2';
            };
            if($request->next_activity != ""){
                $items->next_activity = $request->next_activity;
                $items->next_activity_at = new DateTime("now");
            };
            if($request->assign_id != ""){
                $items->assign_id = $request->assign_id;
                $items->assign_at = new DateTime("now");
            };
            $items->updated_by = Auth::user()->id;
            $items->updated_at = new DateTime("now");
            $items->save();
        };
        session()->flash('message','Mass Updated Successfully');
        return back();
    }

    public function massDestroyLead(Request $request){
        $leadarray = $request->leads_id;
        $leadarray = explode(",",$leadarray[0]);
        $leadlength = count($leadarray);
        for($i=0; $i < $leadlength; $i++){ 
            $id = $leadarray[$i];
            $item = lead::find($id);
            $item->updated_by = Auth::user()->id;
            $item->updated_at = new DateTime("now");
            $item->is_delete = '1';
            $item->save();
            //===== lead logs =======================================
            $logitem = new Lead_log;
            $logitem->lead_id = $id;
            $logitem->name = 'Deleted Successfully';
            $logitem->description = 'Lead have deleted Successfully';
            $logitem->created_by = Auth::user()->id;
            $logitem->save();
        };
        session()->flash('message','Mass Delete Successfully');
        return back();
    }

    public function updateOnlyNextActivity(Request $request){
        $id = $request->lead_id;
        $items = lead::find($id);
        $this->validate($request,[
            'lead_id'=>'required',
            'next_activity'=>'required',
        ]);
        $items->next_activity = $request->next_activity;
        //dd($items);
        $items->save();
        session()->flash('message','Next Activity Updated Successfully');
        return back();
    }

    public function ajaxLeadComment(Request $request){
        if($request->has('id')){
            $id = $request->id;
            $item = View_lead::where('id','=',$id)->count('id');
            if ($item) {
                $resultData = View_lead::select('id','telecaller_comment','comment','tc_comment','bdm_comment')->where('id','=',$id)->get();
                /* ->latest('id')->first(); , ->latest()->first(); , ->latest('id')->first();
                $resultData = Lead::find($id);*/
            } else {
                $resultData = '0';
            }
        }else{
            $resultData = '0';
        }
        return $resultData;
    }

    public function insertTelecallerComment(Request $request){
        $items = new Lead_comment;
        $this->validate($request,[
            'lead_id'=>'required',
        ]);
        $leadid = $request->lead_id;
        $items->lead_id = $leadid;
        $items->type = 'Telecaller activity comment';
        $items->comment = $request->comment;
        if($request->telecaller_comment != ""){
            $items->telecaller_comment = $request->telecaller_comment;
        }else{
            $items->telecaller_comment = 'Called';
        };
        $items->bdm_comment = $request->bdm_comment;
        $isnext_activity = $request->next_activity;
        if ($isnext_activity) {
            $items->next_activity = $isnext_activity;
        };
        if($request->next_activity_at != ""){
            $items->next_activity_at = $request->next_activity_at;
        };        
        $items->created_by = Auth::user()->id;
        $items->save();
        $id = $items->id;
        /*Rest Data inserted ============================*/
        $item = Lead::find($leadid);
        $item->comment_id = $id;
        $item->save();
        session()->flash('message','Comment Post Successfully');
        return back();
    }

    public function ajaxxCallTelecallerComment(Request $request){
        if($request->has('id')){
            $id = $request->id;
            $item = Lead_comment::where('lead_id','=',$id)->count('id');
            if ($item) {
                /*id, lead_id, type, comment, telecaller_comment, bdm_comment, view, is_delete, created_by, updated_by, created_at, updated_at*/
                $resultData = Lead_comment::select('id','lead_id','comment','telecaller_comment','next_activity_at','bdm_comment')->where('lead_id','=',$id)->latest('id')->first();
                //->latest()->first(); , ->latest('id')->first();
                //$resultData = Lead::find($id);
            } else {
                $resultData = '0';
            }
        }else{
            $resultData = '0';
        }
        return $resultData;
    }

    public function ajaxCallTelecallerComment(Request $request){
        if($request->has('id')){
            $id = $request->id;
            $leadid = $request->leadid;
            $item = Lead_telecaller_log::where('id','=',$id)->where('lead_id','=',$leadid)->count('id');
            if ($item) {
                $resultData = View_lead_telecaller_log::select('id','lead_id','mobile','telecaller_comment','next_call_at','service','company','call_status_id')->where('id','=',$id)->where('lead_id','=',$leadid)->get();
                /* ->latest('id')->first(); , ->latest()->first(); , ->latest('id')->first();
                $resultData = Lead::find($id);*/
            } else {
                $resultData = '0';
            }
        }else{
            $resultData = '0';
        }
        return $resultData;
    }

    public function insertTelecallerCall(Request $request){
        //dd('Welcome TC');
        $telecall = new Lead_telecaller_log;
        $this->validate($request,[
            'lead_id'=>'required',
            'mobile'=>'required',
        ]);
        $lead_id = $request->lead_id;
        $telecall->lead_id = $lead_id;
        $leadrow = Lead::select('id','name','company_id','contact_person')->where('id','=',$lead_id)->get();
        $telecall->company_id = $leadrow[0]->company_id;
        $telecall->name = 'Called';
        $telecall->telecaller_id = Auth::user()->id;
        $telecall->telecaller_name = Auth::user()->name;
        $comment = 'I have called this person.';
        $telecall->telecaller_comment = 'Called';
        $telecall->contact_person = $leadrow[0]->contact_person;
        $telecall->mobile = $request->mobile;
        $telecall->callstart_at = new DateTime("now");
        $telecall->created_by = Auth::user()->id;
        $telecall->save();
        $id = $telecall->id;
        /*Rest Data inserted ============================*/
        $lcitem = new Lead_comment;
        $lcitem->lead_id = $lead_id;
        $lcitem->type = 'Telecaller calling';
        $lcitem->telecaller_comment = 'Called';
        $lcitem->bdm_comment = $request->bdm_comment;
        if ($request->next_activity != "") {
            $lcitem->next_activity = $request->next_activity;
        };
        if($request->next_activity_at != ""){
            $lcitem->next_activity_at = $request->next_activity_at;
        };
        $lcitem->created_by = Auth::user()->id;
        $lcitem->created_at = new DateTime("now");
        $lcitem->save();
        $cmid = $telecall->id;
        $items = Lead::find($lead_id);
        $items->telecaller_id = $id;
        $items->comment_id = $cmid;
        $items->save();
        //dd($id);
        return $id;
    }

    public function updateTelecallerCall(Request $request){
        $id = $request->id;
        $item = Lead_telecaller_log::find($id);
        /*dd($id);*/
        if ($request->telecaller_comment != $item->telecaller_comment) {
            $item->telecaller_comment = $request->telecaller_comment;
        } else {
            /*$item->telecaller_comment = 'Called';*/
        };
        if($request->next_activity != ""){
            $item->next_activity = $item->next_activity;
        };
        if($request->next_activity_at != ""){
            $item->next_call_at = $item->next_activity_at;
        };
        $callend = $request->telecallend_at;
        if ($callend > $item->callstart_at) {
            $item->callend_at = $callend;
        } else {
            $item->callend_at = new DateTime("now");
        };
        $item->call_status = $request->telcall_status;
        if ($request->tel_status) {
            $item->status = $request->tel_status;
        } else {
            $item->status = 1;
        };
        $item->updated_by = Auth::user()->id;
        $item->updated_at = new DateTime("now");
        $item->save();
        $leadid = $item->lead_id;
        /*Rest Data inserted ============================*/
        $lcitem = new Lead_comment;
        $lcitem->lead_id = $leadid;
        $lcitem->type = 'Telecaller call end';
        $lcitem->telecaller_comment = $request->telecaller_comment;
        $lcitem->bdm_comment = $request->bdm_comment;
        if ($request->next_activity != "") {
            $lcitem->next_activity = $request->next_activity;
        };
        if($request->next_activity_at != ""){
            $lcitem->next_activity_at = $request->next_activity_at;
        };
        $lcitem->created_by = Auth::user()->id;
        $lcitem->created_at = new DateTime("now");
        $lcitem->save();
        $cmid = $lcitem->id;
        $itemlead = Lead::find($leadid);
        $itemlead->comment_id = $cmid;
        if ($request->next_activity != "") {
            $itemlead->next_activity = $request->next_activity;
        };
        if($request->next_activity_at != ""){
            $itemlead->next_activity_at = $request->next_activity_at;
        };
        $itemlead->save();
        session()->flash('message','Called Successfully');
        return back();
        /*return $id;*/
    } 

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo "create";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $items = new lead;
        $this->validate($request,[
            'name'=>'required',
            'contact_person_mobile'=>'required',
            ]);
        $items->lead_sn = $this->leadgenerate_refkey();
        $items->name = $request->name;
        $items->assign_id = $request->assign_id;
        $items->assign_at = new DateTime("now");
        $items->type = 'New Lead';
        $items->advance_amount = $request->advance_amount;
        $items->total_amount = $request->total_amount;
        $items->source = $request->source;
        $items->description = $request->description;
        $items->meeting_at = $request->meeting_at;
        $items->meeting_message = $request->meeting_message;
        $next_activity = $request->next_activity;
        if ($next_activity) {
            $items->next_activity = $next_activity;
        } else {
            $items->next_activity = 1;
        };
        $items->next_activity_message = $request->next_activity_message;
        $next_activity_at = $request->next_activity_at;
        if ($next_activity_at) {
            $items->next_activity_at = $request->next_activity_at;
        } else {
            /*$items->next_activity_at = new DateTime("now");*/
            /*$items->next_activity_at = '';*/
        };
        //$items->company_id = $request->company_id;
        $is_company = $request->company_id;
        if ($is_company) {
            $items->company_id = $request->company_id;
        }else{
            $comitem = new Companie;
            $this->validate($request,[
                'company_id'=>'required',
                'contact_person_mobile'=>'required',
            ]);
            $comitem->name = $request->namecompany;
            $comitem->contact_person = $request->contact_person;
            $comitem->contact_person_email = $request->contact_person_email;
            $comitem->contact_person_mobile = $request->contact_person_mobile;
            $comitem->landline_no = $request->landline_no;
            $comitem->contact_person_role = $request->contact_person_role;
            $comitem->description = 'Create Company Infomation';
            $comitem->created_by = Auth::user()->id;
            $comitem->save();
            $items->company_id = $comitem->id;
        };
        $items->contact_person_id = $request->contact_person_id;
        $items->contact_person = $request->contact_person;
        $items->contact_person_designation = $request->contact_person_designation;
        $items->contact_person_mobile = $request->contact_person_mobile;
        $items->landline_no = $request->landline_no;
        $items->contact_person_email = $request->contact_person_email;
        $items->contact_person_address = $request->contact_person_address;
        $items->stage = 1;
        $items->status = 1;
        $items->authority_id = Auth::user()->id;
        $items->created_by = Auth::user()->id;
        //dd($items);
        $items->save();
        session()->flash('message','Created Successfully');
        /*====================== Open Mail ==================*/
        $assign = $request->assign_id;
        if ($assign) {
            $userdata = User::select('id','name','email')->where('id', '=', $assign)->get();
            $assignemail = $userdata[0]->email;
            //dd($assignemail);
            $user_id = Auth::user()->id;
            $user = Auth::user()->name;
            $message = 'New laed assigned successfully';
            $data = array(
                "title" => "hello",
                "description" => "test test test"
            );
            //$mailresult = Mail::to($assignemail)->send(new taskmail($items));
            //=== Save in DB =================================================
            $mailitem = new Mail_action_log;
            $mailuser = User::where('id',$items->assign_id)->get();
            $leadid = $items->lead_sn;
            $mailfrom = 'yogeshsoni.developer@gmail.com';
            $mailto = $mailuser[0]['email'];
            $mail_assign = $items->assign_id;
            $mailname = $items->name;
            $mail_at = $items->assign_at;
            $mailitem->from = $mailfrom;
            $mailitem->to = $mailto;
            $mailitem->cc = '';
            $mailitem->bcc = '';
            $mailitem->title = 'LEAD-CRM-SYSTEM | ADMIN';
            $mailitem->subject = 'New lead create and assigne successfully at '.$mail_at.'. Laed id: '.$leadid.', Service: '.$mailname.'';
            $mailitem->server = 'gmail mail server';
            $mailitem->created_by = Auth::user()->id;
            $mailitem->save();
        };
        //dd($mailitem);
        /*====================== End Mail ==================*/
        /*===== Lead_comment =====================================*/
        $ldcomment = new Lead_comment;
        $ldcomment->lead_id = $items->id;
        $ldcomment->type = 'New Laed Created.';
        $ldcomment->telecaller_comment = $items->next_activity_message;
        $ldcomment->created_by = Auth::user()->id;
        $ldcomment->save();
        //===== lead_assigned_member ==============================
        $assignitem = new Lead_assigned_member;
        $assignitem->lead_id = $items->id;
        $assignitem->assign_id = $request->assign_id;
        $assignitem->assign_at = new DateTime("now");
        $assignitem->remark = 'New laed assigned.';
        //$assignitem->user = User::where('id',$request->assign_id)->firstOrFail();
        $assignuser = User::where('id',$request->assign_id)->get('name');
        $assignitem->user = $assignuser[0]['name'];
        $assignitem->created_by = Auth::user()->id;
        $assignitem->save();
        //===== next_activity_logs ================================
        $naitem = new Lead_next_activity_log;
        $naitem->lead_id = $items->id;
        $naid = $request->next_activity;
        if ($naid) {
            $activity_id = $naid;
        }else{
            $activity_id = 1;
        };
        $naitem->activity_id = $activity_id;
        $next_activitie = Lead_next_activitie::find($activity_id);
        $naitem->name = $next_activitie->name;
        $naitem->description = $request->next_activity_message;
        $naitem->datetime_at = $request->next_activity_at;
        $naitem->status = 'New';
        $naitem->class = 'bg-info';
        $naitem->created_by = Auth::user()->id;
        $naitem->save();
        //=====  =======================================
        $lmitem = new Lead_meeting_log;
        $lmitem->lead_id = $items->id;
        $lmitem->name = 'Lead Meeting';
        $lmitem->description = $request->meeting_message;
        $lmitem->meeting_at = $request->meeting_at;
        $lmitem->status = 'New';
        $lmitem->class = 'bg-info';
        $lmitem->created_by = Auth::user()->id;
        $lmitem->save();
        //=====  =======================================
        $ltitem = new Lead_account_transaction; //lead_account_transactions
        $ltitem->lead_id = $items->id;
        $ltitem->name = 'Lead Amount Transaction';
        $ltitem->transaction_id = 'new lead';
        $ltitem->discount_amount = $request->discount_amount;
        $ltitem->advance_amount = $request->advance_amount;
        $ltitem->total_amount = $request->total_amount;
        $ltitem->payment_mathod = $request->payment_mathod;
        $ltitem->created_by = Auth::user()->id;
        $ltitem->save();
        //=====  =======================================
        /*$lcpitem = new Lead_contact_person; //lead_account_transactions
        $lcpitem->lead_id = $items->id;
        $lcpitem->company_id = $request->company_id;
        $lcpname = $request->contact_person;
        if ($lcpname) {
            $lcpitem->name = $request->contact_person;
        } else {
            $lcpitem->name = 'Decision maker';
        };
        $lcpitem->designation = $request->contact_person_designation;
        $lcpitem->mobile = $request->contact_person_mobile;
        $lcpitem->email = $request->contact_person_email;
        $lcpitem->address = $request->contact_person_address;
        $lcpitem->description = 'Created new Lead contact person.';
        $lcpitem->created_by = Auth::user()->id;
        $lcpitem->save();*/
        //===== lead logs =======================================
        $logitem = new Lead_log;
        $logitem->lead_id = $items->id;
        $logitem->name = 'New Lead';
        $logitem->description = 'Have created & assigned Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        return back();
        //return redirect('/inventory-lead');
    }

    public function leadAddMeeting(Request $request){
        //dd('Hello');
        $items = new Lead_meeting_log;
        $this->validate($request,[
            'lead_id'=>'required',
            ]);
        $lead_id = $request->lead_id;
        $items->lead_id = $request->lead_id;
        $items->name = 'Lead Meeting';
        $items->description = $request->description;
        $items->meeting_at = $request->meeting_at;
        $items->status = $request->status;
        $status = $request->status;
        if ($status == 'Done') {
            $items->class = 'bg-success';
        }elseif ($status == 'New') {
            $items->class = 'bg-info';
        }elseif ($status == 'Cancel') {
            $items->class = 'bg-danger';
        }else {
            $items->class = 'bg-warning';
        };
        $items->created_by = Auth::user()->id;
        $items->save();
        //===== lead logs =======================================
        $logitem = new Lead_log;
        $logitem->lead_id = $lead_id;
        $logitem->name = 'Meeting Created Successfully';
        $logitem->description = 'New meeting have created Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        //dd($items->id);
        //return redirect('/inventory-lead/$lead_id');
        return back();
    }

    public function leadAddNextActivity(Request $request){
        //dd('Hello');
        $items = new Lead_next_activity_log;
        $this->validate($request,[
            'lead_id'=>'required',
            ]);
        $lead_id = $request->lead_id;
        $items->lead_id = $request->lead_id;
        $items->activity_id = $request->activity_id;
        $activity_id = $request->activity_id;
        $next_activitie = Lead_next_activitie::find($activity_id);
        $items->name = $next_activitie->name;
        $items->description = $request->description;
        $items->datetime_at = $request->datetime_at;
        $items->status = $request->status;
        $status = $request->status;
        if ($status == 'Done') {
            $items->class = 'bg-success';
        }elseif ($status == 'New') {
            $items->class = 'bg-info';
        }elseif ($status == 'Cancel') {
            $items->class = 'bg-danger';
        }else {
            $items->class = 'bg-warning';
        };
        $items->created_by = Auth::user()->id;
        $items->save();
        //===== lead logs =======================================
        $logitem = new Lead_log;
        $logitem->lead_id = $lead_id;
        $logitem->name = 'Next Activity';
        $logitem->description = 'New next activity have created Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        return back();
    }

    public function updateNextactivity(Request $request){
        $items = new Lead_next_activity_log;
        $this->validate($request,[
            'lead_id'=>'required',
            ]);
        $lead_id = $request->lead_id;
        $activity_id = $request->activity_id;
        $next_activitie = Lead_next_activitie::find($activity_id);
        $items->name = $next_activitie->name;
        $items->lead_id = $lead_id;
        $items->activity_id = $activity_id;
        $items->description = $request->description;
        $items->datetime_at = $request->datetime_at;
        $items->status = 'New';
        $items->class = 'bg-info';
        $items->created_by = Auth::user()->id;
        $items->save();
        //===== Lead update =====================================
        $leaditems = lead::find($lead_id);
        $leaditems->next_activity = $activity_id;
        $leaditems->next_activity_message = $request->description;
        $leaditems->save();
        //===== lead logs =======================================
        $logitem = new Lead_log;
        $logitem->lead_id = $lead_id;
        $logitem->name = 'Next Activity';
        $logitem->description = 'New next activity have created Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        return back();
    }

    
    public function leadAddContactPerson(Request $request){
        $items = new Lead_contact_person;
        $this->validate($request,[
            'name'=>'required',
            'lead_id'=>'required',
        ]);
        $lead_id = $request->lead_id;
        $items->lead_id = $lead_id;
        $items->company_id = $request->company_id;
        $items->employee_id = $request->employee_id;
        $items->name = $request->name;
        $items->designation = $request->designation;
        $items->mobile = $request->mobile;
        $items->email = $request->email;
        $items->fax = $request->fax;
        $items->address = $request->address;
        $items->description = 'Contact Person Infomation';
        $items->created_by = Auth::user()->id;
        $items->save();
        //===== lead logs =======================================
        $litem = Lead::find($lead_id);
        $litem->contact_person = $request->name;
        $litem->contact_person_designation = $request->designation;
        $litem->contact_person_mobile = $request->mobile;
        $litem->contact_person_email = $request->email;
        /*$litem->save();*/
        $logitem = new Lead_log;
        $logitem->lead_id = $items->id;
        $logitem->name = 'Contact Person';
        $logitem->description = 'New contact person have created Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        return back();
    }

    public function ajaxAddNextActivity(Request $request){
        $items = new Lead_next_activitie;
        $this->validate($request,[
            'name'=>'required',
            ]);
        $items->name = $request->name;
        $items->class = $request->class;
        $items->description = 'Create lead next activitie infomation';
        $items->created_by = Auth::user()->id;
        $items->save();
        return $items->id;
    }

    public function ajaxAddCompany(Request $request){
        $items = new Companie;
        $this->validate($request,[
            'name'=>'required',
            'contact_person_mobile'=>'required',
        ]);
        $items->name = $request->name;
        $items->mid_name = $request->mid_name;
        $items->last_name = $request->last_name;
        $items->mobile = $request->mobile;
        $items->mobile1 = $request->mobile1;
        $items->mobile2 = $request->mobile2;
        $items->email = $request->email;
        $items->email2 = $request->email2;
        $items->fax = $request->fax;
        $items->website = $request->website;
        $items->address = $request->address;
        $items->landmark = $request->landmark;
        $items->city = $request->city;
        $items->district = $request->district;
        $items->state = $request->state;
        $items->country = $request->country;
        $items->zipcode = $request->zipcode;
        $items->about = $request->about;
        $items->logo = $request->logo;
        $items->company_id = $request->company_id;
        $items->tax_id = $request->tax_id;
        $items->tin_no = $request->tin_no;
        $items->gst_no = $request->gst_no;
        $items->gst_details = $request->gst_details;
        $items->pan_no = $request->pan_no;
        $items->pan_photo = $request->pan_photo;
        $items->bank_name = $request->bank_name;
        $items->bank_ifsc = $request->bank_ifsc;
        $items->bank_ac = $request->bank_ac;
        $items->bank_branch = $request->bank_branch;
        $items->owner_name = $request->owner_name;
        $items->contact_person = $request->contact_person;
        $items->contact_person_email = $request->contact_person_email;
        $items->contact_person_mobile = $request->contact_person_mobile;
        $items->contact_person_role = $request->contact_person_role;
        $items->description = 'Create Company Infomation';
        $items->created_by = Auth::user()->id;
        $items->save();
        //===== Event action  logs =======================================
        //use Illuminate\Support\Facades\Request;
        /*$eventlog = new Event_action_log;
        $eventlog->name = 'Company Created Successfully';
        //$ip = $request->ip(); //'50.90.0.1'; Request::ip();
        $ip = $_SERVER["REMOTE_ADDR"];
        $eventlog->ip = $ip;
        if (Browser::isMobile()) {
            $useDevice = "Mobile";
        }elseif (Browser::isDesktop()) {
            $useDevice = "Desktop";
        }elseif (Browser::isTablet()) {
            $useDevice = "Tablet";
        };
        $eventlog->browser = Browser::browserName();
        $eventlog->device = $useDevice;
        $eventlog->os = Browser::platformName();
        $browserdetails = Browser::detect();
        $eventlog->client_details = json_encode($browserdetails);
        #$eventlog->client_details = Browser::detect();
        if ($ip == '127.0.0.1' || $ip == '127.0.0.0') {
            $eventlog->location = 'Local Host';
            $eventlog->address = 'Local Host';
        }else{
            $locationData = \Location::get($ip);
            $jsondata = json_encode($locationData);
            $eventlog->location = $jsondata;
            $eventlog->address = $locationData->cityName;
        };
        $eventlog->module = 'Company';
        $eventlog->function = 'Created';
        $eventlog->table_name = 'companies';
        $eventlog->row_id = $items->id;
        $eventlog->previous_data = '';
        $eventlog->current_data = '';
        $eventlog->description = 'Company have created Successfully';
        $eventlog->created_by = Auth::user()->id;
        //dd($eventlog);
        $eventlog->save();*/
        //===== End Event action  logs ===================================
        return $items->id;
    }

    public function ajaxCreateReference(Request $request){
        $items = new Lead_source;
        $this->validate($request,[
            'name'=>'required',
            ]);
        $items->name = $request->name;
        $items->description = 'Create Lead Source Infomation';
        $items->created_by = Auth::user()->id;
        $items->save();
        return $items->id;
    }

    public function leadUpdateStatus(Request $request){
        $id = $request->lead_id;
        $items = lead::find($id);
        $on_status = $request->status;
        $items->status = $on_status;
        if ($on_status == '2' || $on_status == '5' || $on_status == '24') {
            $items->stage = '3';
        }else {
            $items->stage = '2';
        };
        $items->save();
        session()->flash('message','Updated Successfully');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $item = View_lead::find($id);
        if ($item){}else{return view('errors.404');};
        if ($siurole == 1) {
            $previous = Lead::where('id', '<', $id)->max('id');
            $next = Lead::where('id', '>', $id)->min('id');
        } else {
            if ($item->assign_id == $siuid) {
            } else {
                return view('errors.403');
            };
            $previous = Lead::where('assign_id', '=', $siuid)->where('id', '<', $id)->max('id');
            $next = Lead::where('assign_id', '=', $siuid)->where('id', '>', $id)->min('id');
        };
        $last_meetings = Lead_meeting_log::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $last_next_activitys = Lead_next_activity_log::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $amount_trns = Lead_account_transaction::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $contact_persons = Lead_contact_person::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->get();
        $stageid = $item->stage_id;
        $stages = Lead_stage::find($stageid);
        $companyid = $item->company_id;
        $company_data = Companie::find($companyid);
        return view('admin.lead.show',compact('item','stages','last_meetings','last_next_activitys','contact_persons','amount_trns','previous','next','company_data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        echo "edit";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $items = Lead::find($id);
        /*dd($items->name);*/
        $this->validate($request,[
            'name'=>'required',
        ]);
        $items->name = $request->name;
        $items->description = $request->description;
        $items->updated_by = Auth::user()->id;
        $items->save();
        session()->flash('message','Updated Successfully');
        /*===== lead logs =======================================*/
        $logitem = new Lead_log;
        $logitem->lead_id = $id;
        $logitem->name = 'Updated Successfully';
        $logitem->description = 'Lead have updated Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        return back();
    }

    public function xxxUpdate(Request $request, $id){
        $items = lead::find($id);
        /*dd($items->name);*/
        $this->validate($request,[
            'name'=>'required',
        ]);
        $items->name = $request->name;
        $items->description = $request->description;
        $items->source = $request->source;
        $items->status = $request->status;
        $on_status = $request->status;
        if ($on_status == '2' || $on_status == '5' || $on_status == '24') {
            $items->stage = '3';
        }else {
            $items->stage = '2';
        };
        $items->company_id = $request->company_id;
        $items->authority_id = $request->authority_id;
        $items->converted_into = $request->converted_into;
        $items->converted_id = $request->converted_id;
        $items->updated_by = Auth::user()->id;
        if ($items->assign_id != $request->assign_id) {
            $items->assign_id = $request->assign_id;
            $items->assign_at = new DateTime("now");
            /*===== lead_re-assigned_member =======================*/
            $assignitem = new Lead_assigned_member;
            $assignitem->lead_id = $id;
            $assignitem->assign_id = $request->assign_id;
            $assignitem->assign_at = new DateTime("now");
            $assignitem->remark = 'Re-assigned laed.';
            $assignuser = User::where('id',$request->assign_id)->get('name');
            /*dd($assignuser[0]['name']);*/
            $assignitem->user = $assignuser[0]['name'];
            $assignitem->created_by = Auth::user()->id;
            $assignitem->save();
            /*===== lead logs =======================================*/
            $logitem = new Lead_log;
            $logitem->lead_id = $id;
            $logitem->name = 'Re-assigned Successfully';
            $logitem->description = 'Lead have Re-assigned Successfully';
            $logitem->created_by = Auth::user()->id;
            $logitem->save();
            /*====================== Open Mail ==================*/
            $assign = $request->assign_id;
            $userdata = user::select('id','name','email')->where('id', '=', $assign)->get();
            $assignemail = $userdata[0]->email;
            /*dd($assignemail);*/
            $user_id = Auth::user()->id;
            $user = Auth::user()->name;
            $message = 'Lead re-assigned successfully';
            $data = array(
                "title" => "hello",
                "description" => "test test test"
            );
            /*$mailresult = Mail::to($assignemail)->send(new TaskReassignmail($items));*/
            /*=== Save in DB =================================================*/
            $mailitem = new Mail_action_log;
            $mailuser = User::where('id',$assign)->get();
            $leadid = $items->lead_sn;
            $mailfrom = 'yogeshsoni.developer@gmail.com';
            $mailto = $mailuser[0]['email'];
            $mail_assign = $items->assign_id;
            $mailname = $items->name;
            $mail_at = $items->assign_at;
            $mailitem->from = $mailfrom;
            $mailitem->to = $mailto;
            $mailitem->cc = 'yogeshsoni.developer@gmail.com';
            $mailitem->bcc = '';
            $mailitem->title = 'LEAD-CRM-SYSTEM | ADMIN';
            $mailitem->subject = 'Lead re-assigne successfully at '.$mail_at.'. Laed id: '.$leadid.', Service: '.$mailname.'';
            $mailitem->server = 'gmail mail server';
            $mailitem->created_by = Auth::user()->id;
            $mailitem->save();
            /*dd($mailitem);*/
            /*====================== End Mail ==================*/
        };
        $items->save();
        session()->flash('message','Updated Successfully');
        /*===== lead logs =======================================*/
        $logitem = new Lead_log;
        $logitem->lead_id = $id;
        $logitem->name = 'Updated Successfully';
        $logitem->description = 'Lead have updated Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //dd('Delete');
        $item = Lead::find($id);
        $item->updated_by = Auth::user()->id;
        $item->updated_at = new DateTime("now");
        $item->is_delete = '1';
        $item->save();
        //===== lead logs =======================================
        $logitem = new Lead_log;
        $logitem->lead_id = $id;
        $logitem->name = 'Deleted Successfully';
        $logitem->description = 'Lead have deleted Successfully';
        $logitem->created_by = Auth::user()->id;
        $logitem->save();
        /*session()->flash('message','Delete Successfully');
        return redirect('/inventory-lead');*/
        return back();
    }

// $task_id = $this->leadgenerate_refkey(1,'lead','T');
    public function leadgenerate_refkey(){
        $last_id = Lead::max('id');
        if ($last_id<9) {
            $result = 'LDSN000'.($last_id+1);
        }elseif ($last_id<99 && $last_id>=9) {
            $result = 'LDSN00'.($last_id+1);
        }elseif ($last_id<999 && $last_id>=99) {
            $result = 'LDSN0'.($last_id+1);
        }elseif ($last_id>=999) {
            $result = 'LDSN'.($last_id+1);
        };
        return $result;
    }


}
