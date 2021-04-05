<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Invoice;
use App\Models\Invoice_item;
use App\Models\View_invoice_item;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Companie;
use App\Models\Term;
use App\Models\Todo_comment;
use Auth;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;
use App\Http\Controllers\Controller;
use PDF;
use Elibyy\TCPDF\Facades\TCPDF;

class InvoiceController extends Controller
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
        $items = Invoice::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $customers = Customer::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $products = Product::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $invoice_id = $this->invoicegenerate_refkey(1,'invoices','INVOICE');
        $product_id = $this->productgenerate_refkey(1,'product','P');
        $cdate = date('d-m-Y');
        return view('admin.invoice',compact('items','invoice_id','product_id','cdate','customers','products'));
    }

    public function productDataAjax(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            /*$data = DB::table("view_leads")->select("id","name")->where('name','LIKE',"%$search%")->get();*/
            $data = Product::where('is_delete', '=', '0')->select("id","name")->where('name','LIKE',"%$search%")->get();
        }else{
            $data = Product::where('is_delete', '=', '0')->select("id","name")->get();
        }
        return response()->json($data);
    }

    public function manageInvoice(){
        $items = Invoice::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        return view('admin.invoice_list',compact('items'));
    }

    
    public function generateInvoicePdf($id){
        $item = Invoice::find($id);
        $invoice_items = View_invoice_item::where('invoice_id', '=', $id)->orderBy('id', 'desc')->get();
        //$customer = Customer::where('id', '=', $item->customer_id)->get();
        $customer = Customer::find($item->customer_id);
        $products = Product::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $terms = Term::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $company = Companie::find(1);
        //==============================================================================================
        $comname=$company->name;
        $GLOBALS['comname'] = $company->name;
        $GLOBALS['invoice_id'] = $item->invoice_sn;
        $GLOBALS['cdate'] = date('d-m-Y');
        $GLOBALS['ccdate'] = date('d-m-Y');
        $GLOBALS['file_name'] = $item->invoice_sn.'-'.date('dmY-his');
        //dd($customer->name);
        //==============================================================================================
        $pdf = new TCPDF();
        $pdf::SetAuthor('Yogesh K Soni');
        $pdf::SetFont('times', '', 10);
        $pdf::SetTitle('invoice pdf');
        $pdf::SetSubject('invoice pdf');
        $pdf::SetMargins(8, 40, 8, true);
        $pdf::SetFontSubsetting(false);
        $pdf::SetFontSize('10px');   
        $pdf::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Custom Header
        $pdf::setHeaderCallback(function($pdf) {
            $pdf->SetY(5); // Position at 5 mm from top
            $pdf->SetFont('times', 'B', 10); // Set font
            // Title
            //$pdf->Cell(0, 10, 'Something new right here!!!', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $imgdata = base64_decode('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');
            $pdf->Image('@'.$imgdata, $x='10', $y='15', $w=30, $h=20, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
            //$pdf->Image(\URL::asset('admin_files/images/logo.png'), '', 0.5, '', 0.5, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            //$pdf->Image(\URL::asset('img/logo.png'), $x='10', $y='15', $w=30, $h=20, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
            // Title
            
            $htmlleft = '<h4 style="font-size:18px;background-color: #b11016;color:white;"> '.$GLOBALS['comname'].' </h4>';
            $htmlright ='<font style="font-size:16px;background-color: #323639;color:white;padding:4px;"> INVOICE </font><font style="font-size:16px;padding:4px;background-color: #f1f1f1;"> ' .$GLOBALS['invoice_id']. ' </font>
              <p><b>Date: '.$GLOBALS['ccdate'].'</b><br><b>Ref No : '.$GLOBALS['invoice_id'].'</b><br><b>Validity : 30 days</b><br><b>Page 1 of '.$pdf->getAliasNbPages().'</b></p>';
            $pdf->writeHTMLCell($w=80, $h=10, $x=10, $y=5, $htmlleft, $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);
            $pdf->writeHTMLCell($w=80, $h=10, $x=120, $y=5, $htmlright, $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);
        });
        // Custom Footer
        $pdf::setFooterCallback(function($pdf) {
            $pdf->SetY(-15); // Position at 15 mm from bottom
            $pdf->SetFont('times', 'I', 8); // Set font
            $cname = ''.$GLOBALS['comname'].'';
            // Page number
            $htmlleft = '<h4 style="font-size:18px;background-color: #b11016;color:white;"> '.$GLOBALS['comname'].' </h4>';
            //$this->writeHTMLCell(0, 10, $htmlleft, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, $cname, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            // Page number
            $pdf->Cell(0, 10, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
            //$pdf->Cell(0, 10, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        });
        //================= pages ======================================
        //$view = \View::make('invoice_pdf');
        $view = \View::make('invoice_pdf',compact('item','invoice_items','comname','company','customer','terms'));
        $html = $view->render();

        $pdf::AddPage('P', 'A4'); // AddPage('L', 'A4');
        $pdf::writeHTML($html, true, false, true, false, '');
        $pdf::lastPage();
        $pdf::Output($GLOBALS['file_name'].'.pdf');
        //$pdf::Output('invoice.pdf', 'D'); // for download tcpdf file
        //exit;
        //================= End ======================================
    }


    public function downloadInvoicePdf($id){
        $item = Invoice::find($id);
        $invoice_items = View_invoice_item::where('invoice_id', '=', $id)->orderBy('id', 'desc')->get();
        //$customer = Customer::where('id', '=', $item->customer_id)->get();
        $customer = Customer::find($item->customer_id);
        $products = Product::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $terms = Term::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $company = Companie::find(1);
        //==============================================================================================
        $comname=$company->name;
        $GLOBALS['comname'] = $company->name;
        $GLOBALS['invoice_id'] = $item->invoice_sn;
        $GLOBALS['cdate'] = date('d-m-Y');
        $GLOBALS['ccdate'] = date('d-m-Y');
        $GLOBALS['file_name'] = $item->invoice_sn.'-'.date('dmY-his');
        //dd($customer->name);
        //==============================================================================================
        $pdf = new TCPDF();
        $pdf::SetAuthor('Yogesh K Soni');
        $pdf::SetFont('times', '', 10);
        $pdf::SetTitle('invoice pdf');
        $pdf::SetSubject('invoice pdf');
        $pdf::SetMargins(8, 40, 8, true);
        $pdf::SetFontSubsetting(false);
        $pdf::SetFontSize('10px');   
        $pdf::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Custom Header
        $pdf::setHeaderCallback(function($pdf) {
            $pdf->SetY(5); // Position at 5 mm from top
            $pdf->SetFont('times', 'B', 10); // Set font
            // Title
            //$pdf->Cell(0, 10, 'Something new right here!!!', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $imgdata = base64_decode('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');
            $pdf->Image('@'.$imgdata, $x='10', $y='15', $w=30, $h=20, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
            //$pdf->Image(\URL::asset('admin_files/images/logo.png'), '', 0.5, '', 0.5, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            //$pdf->Image(\URL::asset('img/logo.png'), $x='10', $y='15', $w=30, $h=20, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
            // Title
            
            $htmlleft = '<h4 style="font-size:18px;background-color: #b11016;color:white;"> '.$GLOBALS['comname'].' </h4>';
            $htmlright ='<font style="font-size:16px;background-color: #323639;color:white;padding:4px;"> INVOICE </font><font style="font-size:16px;padding:4px;background-color: #f1f1f1;"> ' .$GLOBALS['invoice_id']. ' </font>
              <p><b>Date: '.$GLOBALS['ccdate'].'</b><br><b>Ref No : '.$GLOBALS['invoice_id'].'</b><br><b>Validity : 30 days</b><br><b>Page 1 of '.$pdf->getAliasNbPages().'</b></p>';
            $pdf->writeHTMLCell($w=80, $h=10, $x=10, $y=5, $htmlleft, $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);
            $pdf->writeHTMLCell($w=80, $h=10, $x=120, $y=5, $htmlright, $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);
        });
        // Custom Footer
        $pdf::setFooterCallback(function($pdf) {
            $pdf->SetY(-15); // Position at 15 mm from bottom
            $pdf->SetFont('times', 'I', 8); // Set font
            $cname = ''.$GLOBALS['comname'].'';
            // Page number
            $htmlleft = '<h4 style="font-size:18px;background-color: #b11016;color:white;"> '.$GLOBALS['comname'].' </h4>';
            //$this->writeHTMLCell(0, 10, $htmlleft, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, $cname, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            // Page number
            $pdf->Cell(0, 10, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
            //$pdf->Cell(0, 10, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        });
        //================= pages ======================================
        //$view = \View::make('invoice_pdf');
        $view = \View::make('invoice_pdf',compact('item','invoice_items','comname','company','customer','terms'));
        $html = $view->render();

        $pdf::AddPage('P', 'A4'); // AddPage('L', 'A4');
        $pdf::writeHTML($html, true, false, true, false, '');
        $pdf::lastPage();
        //$pdf::Output($GLOBALS['file_name'].'.pdf');
        $pdf::Output($GLOBALS['file_name'].'.pdf', 'D'); // for download tcpdf file
        //exit;
        //================= End ======================================
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
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
        //return $request;
        $items = new Invoice;
        $this->validate($request,[
            'name'=>'required',
            ]);
        $invoice_sid = $this->invoicegenerate_refkey(1,'invoices','INVOICE');
        $items->invoice_sn = $invoice_sid;
        $items->name = 'Invoice-'.date('dmY');
        $items->invoice_date = new DateTime("now");
        $items->description = $request->description;
        $items->due_date = $request->due_date;
        $items->customer_id = $request->customer_id;
        $items->total_tax = $request->total_tax;
        $items->total_discount = $request->total_discount;
        $items->total_shipping = $request->total_shipping;
        $items->total_sub_amount = $request->total_sub_amount;
        $items->total_amount = $request->total_amount;
        $items->payment_mode = $request->payment_mode;
        $items->status = $request->status;
        $items->created_by = Auth::user()->id;
        //$items->save();
        //if (true) {
        if ($items->save()) {
            $id = $items->id;
            foreach ($request->product_id as $key=>$invitem){
                $invitem = new Invoice_item;
                $invitem->invoice_id = $id;
                $invitem->product_id = $request->product_id[$key];
                $invitem->quantity = $request->quantity[$key];
                $invitem->unit_price = $request->unit_price[$key];
                $invitem->tax = $request->tax[$key];
                $invitem->subtotal = $request->subtotal[$key];
                $invitem->created_by = Auth::user()->id;
                //echo "value[".$key."]: id[".$id."]".$request->product_id[$key]."<br>";
                $invitem->save();
            }
        }
        return redirect('/inventory-invoice-list');
    }



    public function storeProduct(Request $request){
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
        $items->save();
        return redirect('/inventory-invoice');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $item = Invoice::find($id);
        $invoice_items = Invoice_item::where('invoice_id', '=', $id)->orderBy('id', 'desc')->get();
        $customers = Customer::where('id', '=', $item->customer_id)->get();
        $products = Product::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $terms = Term::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        return view('admin.invoice_show',compact('item','invoice_items','customers','products','terms'));
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
    public function destroy($id){
        $item = Invoice::find($id);
        $item->updated_by = Auth::user()->id;
        $item->updated_at = new DateTime("now");
        $item->is_delete = '1';
        $item->save();
        // session()->flash('message','Delete Successfully');
        return redirect('/inventory-invoice-list');
    }

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

    public function invoicegenerate_refkey($id, $tablename, $refname){
        $id=$id;     //LDSN0001
        $tablename=$tablename;
        $refname=$refname;
        $last_id = Invoice::max('id');
        //$last_id = project::all()->last();
        //$last_id = 9994;
        if ($last_id<9) {
            $result = 'INVOICE000'.($last_id+1);
        }elseif ($last_id<99 && $last_id>=9) {
            $result = 'INVOICE00'.($last_id+1);
        }elseif ($last_id<999 && $last_id>=99) {
            $result = 'INVOICE0'.($last_id+1);
        }elseif ($last_id>=999) {
            $result = 'INVOICE'.($last_id+1);
        }
        return $result;
    }


}
