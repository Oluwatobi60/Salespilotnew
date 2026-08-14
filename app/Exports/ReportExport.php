<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $viewName;
    protected $data;

    public function __construct(string $viewName, array $data)
    {
        $this->viewName = $viewName;
        $this->data = $data;
    }

    public function view(): View
    {
        return view($this->viewName, $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
