<?php

namespace App\Exports;

use App\User;
use App\Models\Lead;
use App\Models\View_lead;
use Maatwebsite\Excel\Concerns\FromQuery;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
/*use Maatwebsite\Excel\Concerns\WithMapping;*/
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
/*use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;*/
use Maatwebsite\Excel\Concerns\Exportable;

class ExportLeadReports implements FromCollection, WithHeadings, WithEvents
//class ExportLeadReports implements FromQuery, WithHeadings, WithColumnFormatting, WithMultipleSheets, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function registerEvents(): array {
    	$styleArray = [
    		'font' => [
    			'bold' => true,
    		]
    	];
        return [
            AfterSheet::class => function (AfterSheet $event) use ($styleArray) {
                $event->sheet->getStyle('A1:J1')->applyFromArray($styleArray);
                /*$event->sheet->setCellValue('E27','=SUM(E2:E26)');*/
            },
        ];
    }

    protected $idrows;

	function __construct($idrows) {
	    $this->idrows = $idrows;
	}

    

	public function headings(): array{
        return [
            '#Sr',
            'Service',
            'Company',
            'Contact Person Mobile',
            'Next Activity',
            'Authority',
            'Assign To',
            'Assign AT',
            'Status',
            'Created AT',
            'Updated AT',
        ];
    }

	public function collection(){
	    $leadarray = $this->idrows;
        $leadarray = explode(",",$leadarray[0]);
        $leadlength = count($leadarray);
        $items = array();
        $itemarray = array('lead_sn','name','company','contact_person_mobile','next_activity','authority','assign','assign_at','status','created_at','updated_at');
        for($i=0; $i < $leadlength; $i++){ 
            $id = $leadarray[$i];
            $items[] = View_lead::select($itemarray)->where('id',$id)->get();
            
        };
	    return collect($items);
	}

}
