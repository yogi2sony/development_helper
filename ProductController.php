<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Products_categorie;
use App\Models\Products_subcategorie;
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

class ProductController extends Controller{
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
        $items = Product::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $product_id = $this->productgenerate_refkey(1,'product','P');
        $products_categories = Products_categorie::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $products_subcategories = Products_subcategorie::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        //return view('admin/lead');
        return view('admin.product',compact('items','product_id','products_categories','products_subcategories'));
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
        //dd('Store');
        $items = new Product;
        $this->validate($request,[
            'name'=>'required',
            ]);
        $items->code = $this->productgenerate_refkey(1,'product','P');
        $items->name = $request->name;
        $items->image = $request->image;
        $items->category_id = $request->category_id;
        $items->subcategory_id = $request->subcategory_id;
        $items->brand = $request->brand;
        $items->service_tax = $request->service_tax;
        $items->cost_price = $request->cost_price;
        $items->sale_price = $request->sale_price;
        $items->stock_units = $request->stock_units;
        $items->description = $request->description;
        $items->created_by = Auth::user()->id;

        $file = $request->file('image');
        $filename = $file->getClientOriginalName();
        $directory = 'public/uploads/product_images/';
        //$directory = 'storage/uploads/product_images/';
        $splitName = explode('.', $filename, 2);
        $date = new DateTime("now");
        $strip = $date->format('YmdHis');
        $path = $directory.$splitName[0].$strip.'.'.$splitName[1];
        $storename = $splitName[0].$strip.'.'.$splitName[1];
        $file->storeAs($directory,  $storename ,'local');
        $items->image = $storename;
        $items->image_url = $path;

        /*if ($request->hasFile('image')){
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            //$directory = public_path()."\uploads\product_images";
            $directory = '/uploads/product_images/';
            //Storage::makeDirectory($directory);
            $splitName = explode('.', $filename, 2);
            $date = new DateTime("now");
            $strip = $date->format('YmdHis');
            //$path = 'storage'.$directory.$splitName[0].$strip.'.'.$splitName[1];
            $path = $directory.$splitName[0].$strip.'.'.$splitName[1];
            $storename = $splitName[0].$strip.'.'.$splitName[1];
            $items->image = $storename;
            $items->image_url = $path;
            $file->storeAs($directory,  $storename ,'local');
        };*/
        $items->save();
        return redirect('/inventory-product');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $item = Product::find($id);
        return view('admin.product_show',compact('item'));
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
        $items = Product::find($id);
        $this->validate($request,[
            //'lead_title'=>'required',
           // 'details'=>'required',
            ]);
        $items->lead_title = $request->lead_title;
        $items->remarks = $request->remarks;
        $items->deadline = $request->deadline;
        $items->next_activity = $request->next_activity;
        $items->reference_by = $request->reference_by;
        $items->updated_by = Auth::user()->id;
        $items->save();
        /*$object= $items->lead_no;
        $event= 'updated';
        adduserhistory($object, $event);
        session()->flash('message','Updated Successfully');*/
        return redirect('/inventory-product');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //dd('Delete');
        $item = Product::find($id);
        $item->updated_by = Auth::user()->id;
        $item->updated_at = new DateTime("now");
        $item->is_delete = '1';
        $item->save();
        // session()->flash('message','Delete Successfully');
        return redirect('/inventory-product');
    }

// $task_id = $this->productgenerate_refkey(1,'lead','T');
    public function productgenerate_refkey($id, $tablename, $refname){
        $id=$id;     //LDSN0001
        $tablename=$tablename;
        $refname=$refname;
        $last_id = Product::max('id');
        //$last_id = project::all()->last();
        //$last_id = 9994;
        if ($last_id<9) {
            $result = 'PSN000'.($last_id+1);
        }elseif ($last_id<99 && $last_id>=9) {
            $result = 'PSN00'.($last_id+1);
        }elseif ($last_id<999 && $last_id>=99) {
            $result = 'PSN0'.($last_id+1);
        }elseif ($last_id>=999) {
            $result = 'PSN'.($last_id+1);
        }
        return $result;
    }


}
