<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ExportUsers;
use App\Imports\ImportUsers;
use Maatwebsite\Excel\Facades\Excel;
use App\User;

use DB;
use App\Models\Lead;
use App\Models\Todo_comment;
use Auth;
//use Input;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;
//use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function importExport()
    {
       return view('import');
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function export() 
    {
        return Excel::download(new ExportUsers, 'users.xlsx');
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function import() 
    {
        Excel::import(new ImportUsers, request()->file('file'));
            
        return back();
    }
}
