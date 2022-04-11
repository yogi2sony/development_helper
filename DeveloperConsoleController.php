<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Lead;
use DB;
use Auth;
use DateTime;

class DeveloperConsoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $title = 'Developer Mode';
        if ($siurole == 1) {
            $result='Hello Admin';
        } else {
            $result='You don\'t have permission to access this resource.';
        }
        return view('admin.developer_console.index',compact('title','result'));
    }

    public function getUpdateLeadContactPerson(){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $title = 'Developer Mode';
        if ($siurole == 1) {
            $NameDatabanti = array("sheetal","Lubnaparveen","QAMRUL","L K Saddi","irtza","vikas verma","Contact Person","ANIL","rahul","ARNAB DAS","Contact Person","Lakshmi Narayana Gorripalli","jeevan","anil","ADHITYA ","Contact Person","Ashish","Contact Person","RAJEEV VERMA","siddharth puri","MOHD.AAKIL","NAGARAJ KUL","Contact Person","tejas","karan","Contact Person","yogesh","Y S RAGHAV","MUKESH TYAGI","Contact Person","zafar","DENIWAL","SUNIL","Amanpreet kaur","Contact Person","KARAN","DEEPAK","MEENA","kundan kumar","THOMSON LILLIE","DINESH","AKSHITA","ANIL POGHADE","AMIT Pandey","NIKHIL BANSAL","SANJAY SADHU","SHUBHAM JAIN","RAHUL","ARYAN","ANIMESH","KULDEEP","NIDHI ","APURVA","KARTIKA","RAJ","AKASH","ASHWANI","Ateeq Mohd. Khan","manish sharma","balwant singh","Contact Person","mr. sarkar","HEMANT","sandeep mann","Contact Person","Sidharth Banerjee","C L GROVER","Contact Person","RITESH JAIN","AMIT GUPTA","BALAKRISHNA","Contact Person","Contact Person","Contact Person","RAJAN","gopal","kanan","kartik","ramakrishna","murgan","Dr Sharma","SMITA PAWAR","RAJA S","TEXT","KRUNAL","3 leads Resources","rajesh","SIMHA","radha","rakesh kumar verma","S P SINGH","divya Roy","alvi gurgaon","HARMONY PLACEMENT","BISHWANATH","GAURAV HINGNE","SHIKH FARHAD","VINAY","MUKESH","KUMAR","DEEPAK","SARIKA","SANJAY DESH PANDEY","rakesh ","AMARNATH BHAKTA","MOHD.ARSHA","SRINIVAS","ANIL KUMAR","PRASHANTH","SAVAN RAMI","SACHIN CHURI","PREETANSHU SHRI","SANJAY SHARMA","PREM","MAYANK BHARGAVA","ABHISHEK PANDEY","SUMIT","SANJAY AMBATKAR","SATYAJEET","VENKATESH GOPAL","MAYANK","MUKUNTHAN","JOY PRANEET","MEHUL TANK","RAMESH","KUNAL PRADHAN","SANKET AGARWAL","SANDEEP AGGARWA","RAJ SINGLA","SHIV PRATAP");

            $NameDataanju = array("deepa/chirag/","prajakta","bhaskar","ruperdra","anil","rahul singh","karandep/hr","awanish","ramsuphal","sayyad","anand ","akki","ajay shimar","Prakash","ashiya/shoaib","arshad","patel ariff","parkash maharashtra","minoli","atul","Dr kashyap","yogesh garg","murthy/venkata","trivender/prince");
            
            foreach ($NameDatabanti as $key => $value) {
                $id = $key+44805;
                $item = Lead::find($id);
                $item->contact_person = $value;
                $item->save();
            }
            foreach ($NameDataanju as $key => $value) {
                $id = $key+45103;
                $item = Lead::find($id);
                $item->contact_person = $value;
                $item->save();
            }
            
            $result='Successfully completed';
        } else {
            $result='You don\'t have permission to access this resource.';
        }
        return view('admin.developer_console.index',compact('title','result'));
    }

    public function getUpdateLeadContact_person_mobile(){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $title = 'Developer Mode';
        if ($siurole == 1) {
            $Databanti = array("9810496042","9008997712","9690213279","7742408300","8713957636","9886771655","9892537484","9312834817","8286927769","9232757814","9731272109","9908714361","9811269746","9820024093","9818658973","9449527030","9443480000","9811141243","9899116186","9810144069","9621691726","9513335043","9444224554","9313522105","9717616999","9810341232","9811424596","9953052942","9845305826","9885858610","9377433586","9810566820","9811065032","9821239973","9845007177","8567885674","9999722851","9123563731","7250060884","9976084858","9975276460","7669011688","9970697999","7977784234","9997061071","9997958058","9756905850","9971006400","9810152413","9831349549","9810408338","9999186800","9909975588","7305056208","9377773114","9351507894","9718360718","9717778601","9350402757","9828517776","9900445992","9845078081","9949073456","9891369629","9900445992","9999775463","9810396596","9210589509","9250706255","9829014953","9866422411","9739099570","9897456321","9985016369","9953425647","9822366747","9841733135","9392518881","9440464671","9994549859","9811369984","9845177000","9845524853","9845864540","9987005901","9632831140","9718253409","9900145910","9866790009","9810013357","9811163657","9811192259","9811218763","9845069946","7069090009","9820913369","9989276387","9987499969","9986862740","9986449789","9986082233","9986287867","9986139305","9986019606","8800414168","9985300598","9985119461","9980033679","9980014523","9586296261","9987007142","9993052995","9999778654","9949781000","9953002911","9958819455","9971422533","9971632893","9972005696","9980072748","9818851426","9994534896","9008026475","9825111282","9985516627","9082721691","9984207656","9988444780","9971716221","9971331899");
            $Dataanju = array("7290037071","7020209048","7975361718","9310335484","6376940342","8506036406","8826292670","9990908902","9810650701","7829534082","9008181808","8684867047","9518404754","9840305931","8170854435","9321008287","8866422174","9167723979","8016469672","6264231291","9712268520","9878483904","8179015157","9682646048");
            
            $xxData = array("9889880001", "9889880002", "9889880003", "9889880004");

            foreach ($Databanti as $key => $value) {
                $id = $key+44805;
                $item = Lead::find($id);
                $item->contact_person_mobile = $value;
                $item->updated_by = Auth::user()->id;
                $item->updated_at = new DateTime("now");
                $item->save();
            }
            foreach ($Dataanju as $key => $value) {
                $id = $key+45103;
                $item = Lead::find($id);
                $item->contact_person_mobile = $value;
                $item->updated_by = Auth::user()->id;
                $item->updated_at = new DateTime("now");
                $item->save();
            }
            /*for($i=0; $i < count($Data); $i++) {
                $row = $result[$i];
            }*/
            $result='Successfully completed';
        } else {
            $result='You don\'t have permission to access this resource.';
        }
        return view('admin.developer_console.index',compact('title','result'));
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
