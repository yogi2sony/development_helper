<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use Auth;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserdetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        //$items = User::all();
        $items = User::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $title='Lead System';
        //return redirect('/dashboard');
        return view('admin.userdetails',compact('items','title'));
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
    public function store(Request $request){
        //dd('Hello register user');
        $items = new User;
        $this->validate($request,[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $items->name = $request->name;
        $items->password = Hash::make($request->password);
        if ($request->password == $request->password_confirmation) {
            $items->email = $request->email;
        }else{
            return redirect('/inventory-users');
        };
        $items->created_by = Auth::user()->id;
        $items->save();
        return redirect('/inventory-users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        echo "Show";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        echo "Edit";
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
        echo "Update";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     echo "<h4 style='text-align: center;margin: 10%;'>You can't delete any user with out permission of administrator... <br><a href='/inventory-users'><i class='fas fa-arrow-left mr-1'></i>  Back </a></h4>";
     */
    public function isActivated($id){
        //dd('isActivated');
        $item = User::find($id);
        $item->updated_by = Auth::user()->id;
        $item->updated_at = new DateTime("now");
        $status = $item->active;
        //dd($status);
        if ($status == 1) {
            $item->active = 0;
        } else {
            $item->active = 1;
        };
        $item->save();
        if ($item->active == 1) {
            session()->flash('message','Activated  Successfully');
        } else {
            session()->flash('message','Deactivated Successfully');
        };
        return back();
    }

    public function destroy($id){
        $item = User::find($id);
        $item->updated_by = Auth::user()->id;
        $item->updated_at = new DateTime("now");
        $item->is_delete = '1';
        $item->save();
        session()->flash('message','Deleted Successfully');
    }
}
