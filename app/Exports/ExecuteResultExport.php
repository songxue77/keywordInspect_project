<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExecuteResultExport implements FromView
{
    private $exportData;
    
    public function __construct($data)
    {
        $this->exportData = $data;
    }
    
    public function view(): View
    {
        return view('eduplan.executeResult.excel_output', [
            'executeResultIdx' => $this->exportData['executeResultIdx'],
            'executeResult' => $this->exportData['executeResult'],
            'executeResultArray' => $this->exportData['executeResultArray'],
            'executeKeywordCount' => $this->exportData['executeKeywordCount']
        ]);
    }
}