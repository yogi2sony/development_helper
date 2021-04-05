<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Lead;
use DB;
use Auth;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $totlead = Lead::where('is_delete', '=', '0')->count('id');
        if ($siurole == 1) {
            $totallead = $totlead;
            $newlead = Lead::where('status', '=', '1')->where('is_delete', '=', '0')->count('id');
            $cvlead = Lead::where('status', '=', '2')->where('is_delete', '=', '0')->count('id');
            
        } else {
            $totallead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->count('id');
            $newlead = Lead::where('status', '=', '1')->where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->count('id');
            $cvlead = Lead::where('status', '=', '2')->where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->count('id');
        }
        $rest = $totallead-($newlead+$cvlead);
        $items = User::select('id','name','active')->where('active', '=', '1')->get();
        foreach ($items as $key => $value) {
            $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $value->id)->count('id');
            $items[$key]["csld"]=$countLead;
        }
        $jsonitems = json_encode($items);
        $result = Lead::where('is_delete', '=', '0')->select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year');
        $resultdata = array();
        for($i=0; $i < count($result); $i++) {
            $cLead = Lead::where('is_delete', '=', '0')->whereYear('created_at', '=', $result[$i])->count('id');
            array_push($resultdata,$cLead);
        }
        return view('admin.dashboard.index',compact('totallead','newlead','cvlead','rest','items','jsonitems','result','resultdata','totlead'));
    }

    public function test($id){
        $data = Crypt::decrypt($id);
        /*$parameter = 'Yogi';
        $encryptData = Crypt::encrypt($parameter);
        $decryptData = Crypt::decrypt($encryptData);
        echo "<br><b>Crypted Data:</b> ".$encryptData;
        echo "<br><b>DeCrypted Data:</b> ".$decryptData;*/
        echo "<br><b>Data:</b> ".$data;
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
