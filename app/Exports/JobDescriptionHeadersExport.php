<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class JobDescriptionHeadersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithCustomCsvSettings
{

    use Exportable;

    public function collection()
    {
        // Return an empty collection
        return new Collection();
    }

    public function headings(): array
    {
        // Define the column headings for the Excel file
        $headings = [
            'المحافظة',
            'الوزارة',
            'الجهة الفرعية',
            'الجهة التابعة',
            'الجهة التابعة الفرعية',
            'العدد المطلوب',
            'المسمى الوظيفي',
            'رقم بطاقة الوصف الوظيفي',
            'الفئة',
            'الجنس المطلوب',
            'الاختصاص العام 1',
            'الاختصاص الدقيق 1',
            'الاختصاص العام 2',
            'الاختصاص الدقيق 2',
            'الاختصاص العام 3',
            'الاختصاص الدقيق 3',
        ];

        return $headings;
    }

    public function styles($sheet)
    {
        // Apply styling to the table headers
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'size' => 20,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '87CEEB'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setRightToLeft(true);

        // Apply styling to the table values
        $sheet->getStyle('A2:P2')->applyFromArray([
            'font' => [
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
        ];
    }
    
}
