<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use PDF;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Models\Todo_comment;
// install: composer require elibyy/tcpdf-laravel
// https://github.com/elibyy/tcpdf-laravel

class DefaultController extends Controller
{
    public function index(){
        $html = '<h1>Hello World</h1>';
        PDF::SetTitle('Hello World');
        PDF::AddPage();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('hello_world.pdf');
    }

    public function indexSecond(){    
        for ($i = 0; $i < 5; $i++) {
            $html = '<h1>Hello World '.$i.'</h1>';
            PDF::SetTitle('Hello World'.$i);
            PDF::AddPage();
            PDF::Write(0, 'Hello World'.$i);
            // Write the file instead of throw it in the browser
            PDF::Output(public_path('hello_world' . $i . '.pdf'), 'F');
            PDF::reset();
        }
    }

    public function indexThird(){
        $html = '<h1>Hello world</h1>';
        $pdf = new TCPDF();
        $pdf::SetTitle('Hello World');
        $pdf::AddPage();
        $pdf::writeHTML($html, true, false, true, false, '');
        $pdf::Output('hello_world.pdf');
    }

    public function indexFourth(){
        $view = \View::make('myview_name');
        $html = $view->render();
        
        $pdf = new TCPDF();
        $pdf::SetTitle('Hello World');
        $pdf::AddPage();
        $pdf::writeHTML($html, true, false, true, false, '');
        $pdf::Output('hello_world.pdf');
    }

}
