<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo_comment;
use DB;
use Auth;
//use Input;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ToDoSheetController extends Controller{
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
        $items = Todo_comment::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        return view('admin.todosheet',compact('items'));
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
        $items = new Todo_comment;
        $this->validate($request,[
            'comment'=>'required',
        ]);
        $items->comment = $request->comment;
        $items->tablename = $request->tablename;
        $items->rowid = $request->rowid;
        $items->status = 1;
        $items->created_by = Auth::user()->id;
        //dd($items);
        $items->save();
        return back();
    }

    public function ajaxInsertdata(Request $request){
        $this->validate($request,[
            'comment'=>'required',
        ]);
        $items = new Todo_comment;
        $items->comment = $request->comment;
        $items->tablename = 'main';
        $items->rowid = '0';
        $items->status = 1;
        $items->created_by = Auth::user()->id;
        $items->save();
        $data = [$items->comment,$items->created_at];
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        
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
    public function update(Request $request, $id){
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        
    }


}
