<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

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
use App\Models\Invoice;
use App\Models\Invoice_item;
use App\Models\Product;
use App\Models\Companie;
use App\Models\Employee;
use App\Models\Customer;

use DB;
use Auth;
use Schema;
use Redirect;
use DateTime;

class AjaxmastercallController extends Controller
{
    /**
     * Master Call AjaxmastercallController
     *
     * @param  int  $id
     * @return View
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function ajaxPostData(Request $request){
        $data = $request->source;
        return $data;
    }

    public function resourceCallMaster(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = Lead_source::select("id","name")->where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->take(10)->get();
        }else{
            $data = Lead_source::select("id","name")->where('is_delete', '=', '0')->take(10)->get();
        }
        return response()->json($data);
    }

    public function nextactivityCallMaster(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = Lead_next_activitie::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->get();
            /*$data = Lead_next_activitie::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->take(10)->get();*/
        }else{
            $data = Lead_next_activitie::where('is_delete', '=', '0')->select("id","name")->get();
            /*$data = Lead_next_activitie::where('is_delete', '=', '0')->select("id","name")->take(10)->get();*/
        }
        return response()->json($data);
    }

    public function companyCallMaster(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = Companie::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->take(10)->get();
        }else{
            $data = Companie::where('is_delete', '=', '0')->select("id","name")->take(10)->get();
        }
        return response()->json($data);
    }

    public function userCallMaster(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = User::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->take(10)->get();
        }else{
            $data = User::where('is_delete', '=', '0')->select("id","name")->take(10)->get();
        }
        return response()->json($data);
    }

    public function companyAjaxCallMaster(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = Companie::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->get();
        }else{
            $data = Companie::where('is_delete', '=', '0')->select("id","name")->take(10)->get();
        }
        return response()->json($data);
    }

    public function createLeadCallMaster(Request $request){
        $lead_id = $this->leadgenerate_refkey();
        return $lead_id;
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