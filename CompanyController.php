<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
#use Illuminate\Support\Facades\Request;
#use Request;
use Browser;
use DB;
use App\Models\Companie;
use App\Models\Term;
use App\Models\Event_action_log;
use App\Models\Todo_comment;
use Auth;
//use Input;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
        $dataval =  'Hello,';
        $browser =  Browser::browserName();
        $platform =  Browser::platformName();
        $allval =  Browser::detect();
        $ip = Request::ip();
        echo "ip: ".$ip."<br>";
        echo "Browser: ".$browser."<br>";
        echo "Operating System: ".$platform."<br>";
        if (Browser::isMobile()) {
            echo "Device: Mobile<br>";
        }elseif (Browser::isDesktop()) {
            echo "Device: Desktop<br>";
        }elseif (Browser::isTablet()) {
            echo "Device: Tablet<br>";
        };
        dd($dataval);

        $ip = '50.90.0.1';
        $data = \Location::get($ip);
        $address = $data->cityName.', '.$data->countryName.', '.$data->zipCode;
        dd($address);
    */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $items = Companie::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(20);
        $title = 'Manage Companies List';
        return view('admin.company',compact('title','items'));
    }

    public function myCompanydata(){
        $items = Companie::where('company_type', '=', '1')->orwhere('company_type', '=', '2')->where('is_delete', '=', '0')->get();
        $title = 'My Company Title';
        return view('admin.company.mycompany',compact('title','items'));
        
    }
    
    public function showMyCompanydata($id){
        $item = Companie::find($id);
        $title = 'Rooys SostTech';
        $action_logs = Event_action_log::where('is_delete', '=', '0')->where('table_name', '=', 'companies')->where('row_id', '=', $id)->orderBy('created_at', 'desc')->get();
        return view('admin.company.mycompany_show',compact('item','title','action_logs'));
    }

    public function searchCompanydata(Request $request){
        if($request->has('q')){
            $q = $request->q;
            $items = Companie::where ('name','LIKE','%'.$q.'%')->orwhere('email','LIKE',"%$q%")->orwhere('mobile','LIKE',"%$q%")->paginate(20);
            $items->appends(['q' => $q]);
            /*
            $items = Companie::where ('name','LIKE','%'.$q.'%')->paginate(20)->setPath('');
            $pagination = $items->appends(array(
                'q' => $q
            ));
            $items = Companie::search($request->q)->where('is_delete', '=', '0')->paginate(20);
            $items = Companie::where('is_delete', '=', '0')->where('name','LIKE',"%$q%")->orwhere('email','LIKE',"%$q%")->orwhere('mobile','LIKE',"%$q%")->simplePaginate(20);*/
        }else{
            $q = "";
            $items = Companie::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(20);
        }
    
        $title = 'Find & Search "'.$q.'" in Companies List';
        return view('admin.company',compact('title','items'));
    }

    public function isExistCompany(Request $request){
        if($request->has('q')){
            $search = $request->q;
            //$item = Companie::find('name','LIKE',"%$search%");
            //$item = Companie::where('name','=',"%$search%")->get();
            $item = Companie::where('name','=',$search)->count('id');
            if ($item) {
                $resultData = '1';
            } else {
                $resultData = '0';
            }
        }else{
            $resultData = '0';
        }
        //$resultData = '0';
        return $resultData;
    }

    public function isCompanyExist(Request $request){
        if($request->has('q')){
            $search = $request->q;
            $item = Companie::where('name','=',$search)->count('id');
            if ($item) {
                $resultData = Companie::select('id','name','contact_person','contact_person_email','contact_person_mobile','contact_person_role','email','mobile','landline_no','fax','website','gst_no','address','city','district','state','country','zipcode')->where('name','=',$search)->get();
                //$resultData = $rowdata['id'];
                //$resultData = '1';
            } else {
                $resultData = '0';
            }
        }else{
            $resultData = '0';
        }
        return $resultData;
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
    /*id, name, mid_name, last_name, mobile, mobile1, mobile2, email, email2, fax, website, address, landmark, city, district, state, country, zipcode, about, logo, company_id, tax_id, tin_no, gst_no, gst_details, pan_no, pan_photo, bank_name, bank_ifsc, bank_ac, bank_branch, description, owner_name, contact_person, contact_person_email, contact_person_mobile, contact_person_role, is_delete, created_by, updated_by, created_at, updated_at*/

    public function store(Request $request){
        $items = new Companie;
        $this->validate($request,[
            'name'=>'required|unique:companies,name',
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
        session()->flash('message','Created Successfully');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $item = Companie::find($id);
        $title = 'Rooys SostTech';
        $action_logs = Event_action_log::where('is_delete', '=', '0')->where('table_name', '=', 'companies')->where('row_id', '=', $id)->orderBy('created_at', 'desc')->get();
        return view('admin.company_show',compact('item','title','action_logs'));
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
        //dd('update');
        $items = Companie::find($id);
        /*dd($items->name);*/
        $this->validate($request,[
            /*'name'=>'required|unique:companies,name',*/
            /*'name' => [
                'required',
                'unique:companies,name,'.$this->id
            ],*/
            'name' => 'required|unique:companies,name,'.$id.''
        ]);
        $cname = $request->name;
        if ($cname == $items->name) {
            # pass
        } else {
            $items->name = $cname;
            $items->mid_name = $request->mid_name;
            $items->last_name = $request->last_name;
        };
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
        $items->updated_by = Auth::user()->id;
        $items->save();
        //===== Event action  logs =======================================
        /*$eventlog = new Event_action_log;
        $eventlog->name = 'Company Updated Successfully';
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
        if ($ip == '127.0.0.1' || $ip == '127.0.0.0') {
            $eventlog->location = 'Local Host';
            $eventlog->address = 'Local Host';
        }else{
            $locationData = \Location::get($ip);
            $ipdata = array(
                "ip" => $locationData->ip,
                "countryName" => $locationData->countryName,
                "countryCode" => $locationData->countryCode,
                "regionCode" => $locationData->regionCode,
                "regionName" => $locationData->regionName,
                "cityName" => $locationData->cityName,
                "zipCode" => $locationData->zipCode,
                "isoCode" => $locationData->isoCode,
                "postalCode" => $locationData->postalCode,
                "latitude" => $locationData->latitude,
                "longitude" => $locationData->longitude,
                "metroCode" => $locationData->metroCode,
                "areaCode" => $locationData->areaCode,
                "title" => "Location",
                "description" => "Location Track"
            );
            $jsondata = json_encode($locationData);
            $eventlog->location = $jsondata;
            $eventlog->address = $locationData->cityName;
            //dd($jsondata);
        };
        $eventlog->module = 'Company';
        $eventlog->function = 'Updated';
        $eventlog->table_name = 'companies';
        $eventlog->row_id = $id;
        $eventlog->previous_data = '';
        $eventlog->current_data = '';
        $eventlog->description = 'Company have updated Successfully';
        $eventlog->created_by = Auth::user()->id;
        //dd($eventlog);
        $eventlog->save();*/
        session()->flash('message','Updated Successfully');
        //===== End Event action  logs ===================================
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $item = Companie::find($id);
        $item->updated_by = Auth::user()->id;
        $item->updated_at = new DateTime("now");
        $item->is_delete = '1';
        $item->save();
        //===== Event action  logs =======================================
        /*$eventlog = new Event_action_log;
        $eventlog->name = 'Company Deleted Successfully';
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
        $eventlog->function = 'Deleted';
        $eventlog->table_name = 'companies';
        $eventlog->row_id = $id;
        $eventlog->previous_data = '';
        $eventlog->current_data = '';
        $eventlog->description = 'Company have deleted Successfully';
        $eventlog->created_by = Auth::user()->id;
        $eventlog->save();*/
        //===== End Event action  logs ===================================
        session()->flash('message','Deleted Successfully');
        return back();
    }
}
