<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ApplicantHeadersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithCustomCsvSettings
{

    use Exportable;

    public function collection()
    {
        // Return an empty collection
        return new Collection();
    }

    public function headings(): array
    {
        return [
            'الاسم الثلاثي',
            'الجنس',
            'اسم الأم',
            'الرقم الوطني',
            'يوم الميلاد',
            'شهر الميلاد',
            'سنة تاريخ الميلاد',
            'المحافظة',
            'مكان الولادة',
            'تاريخ التخرج',
            'معدل التخرج',
            'الفئة',
            'الاختصاص العام',
            'الاختصاص الدقيق',
            'المحافظة المرغوبة',
            'ملاحظات',
            'المسمى الوظيفي 1',
            'الوزارة 1',
            'رقم بطاقة الوصف 1',
            'المسمى الوظيفي 2',
            'الوزارة 2',
            'رقم بطاقة الوصف 2',
            'المسمى الوظيفي 3',
            'الوزارة 3',
            'رقم بطاقة الوصف 3',
        ];
    }

    public function styles($sheet)
    {
        // Apply styling to the table headers
        $sheet->getStyle('A1:Y1')->applyFromArray([
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
        $sheet->getStyle('A2:Y100')->applyFromArray([
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
