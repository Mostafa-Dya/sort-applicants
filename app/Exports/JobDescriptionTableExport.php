<?php

namespace App\Exports;

use App\Models\JobDescriptionTable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class JobDescriptionTableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $jobDescriptions;

    public function __construct(Collection $jobDescriptions)
    {
        $this->jobDescriptions = $jobDescriptions;
    }

    public function collection()
    {
        // Transform the job descriptions data before exporting
        $formattedJobDescriptions = $this->jobDescriptions->map(function ($jobDescription) {
            $gender = $jobDescription->gender_needed === 0 ? 'زكور' : ($jobDescription->gender_needed === 1 ? 'إناث' : '-');
            
            // Initialize specialization data with empty values
            $specializationData = [
                'الدرجة العلمية 1' => '-',
                'التخصص المطلوب 1' => '-',
                'الدرجة العلمية 2' => '-',
                'التخصص المطلوب 2' => '-',
                'الدرجة العلمية 3' => '-',
                'التخصص المطلوب 3' => '-',
            ];
    
            // Retrieve specialization data from the job description's relationship
            $specializations = $jobDescription->specializationNeeded;
            foreach ($specializations as $index => $specialization) {
                // Translate degrees to Arabic
                $degreeTranslation = [
                    'Ph.D' => 'دكتوراه',
                    'Master' => 'ماجستير',
                    'Bachelor' => 'إجازة',
                    'Diploma' => 'دبلوم',
                    'Intermediate-institute' => 'معهد متوسط',
                    'Higher-Institute' => 'تعليم عالي',
                    'High-school' => 'ثانوي',
                    'Basic-education' => 'تعليم أساسي',
                    'Craftsman' => 'حرفي',
                ];
    
                $specializationData['الدرجة العلمية ' . ($index + 1)] = $degreeTranslation[$specialization->degree] ?? '-';
                $specializationData['التخصص المطلوب ' . ($index + 1)] = $specialization->specialization_needed;
            }
    
            return [
                'المحافظة' => $jobDescription->governorate ?: '-',
                'الوزارة' => $jobDescription->public_entity ?: '-',
                'الجهة الفرعية' => $jobDescription->sub_entity ?: '-',
                'الجهة التابعة' => $jobDescription->affiliate_entity ?: '-',
                'الجهة التابعة الفرعية' => $jobDescription->sub_affiliate_entity ?: '-',
                'الاختصاص العام' => $jobDescription->general ?: '-',
                'الاختصاص الدقيق' => $jobDescription->precise ?: '-',
                'العدد المطلوب' => $jobDescription->vacancies ?: '-',
                'المسمى الوظيفي' => $jobDescription->job_title ?: '-',
                'رقم بطاقة الوصف الوظيفي' => $jobDescription->card_number ?: '-',
                'الفئة' => $jobDescription->category ?: '-',
                'الجنس المطلوب' => $gender,
                ...$specializationData, // Spread operator to merge specialization data
            ];
        });
    
        return $formattedJobDescriptions;
    }
    
    
    
    

   
    


    private function mapCategory($category)
    {
        // Map category values to their corresponding labels
        switch ($category) {
            case 1:
                return 'Category 1';
            case 2:
                return 'Category 2';
            case 3:
                return 'Category 3';
            case 4:
                return 'Category 4';
            case 5:
                return 'Category 5';
            default:
                return '-';
        }
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
            'الاختصاص العام',
            'الاختصاص الدقيق',
            'العدد المطلوب',
            'المسمى الوظيفي',
            'رقم بطاقة الوصف الوظيفي',
            'الفئة',
            'الجنس المطلوب',
            'الدرجة العلمية 1',
            'التخصص المطلوب 1',
            'الدرجة العلمية 2',
            'التخصص المطلوب 2',
            'الدرجة العلمية 3',
            'التخصص المطلوب 3'
        ];
        

        return $headings;
    }

    public function styles($sheet)
    {
        // Apply styling to the table headers
        $sheet->getStyle('A1:R1')->applyFromArray([
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
        $sheet->getStyle('A2:R' . ($this->jobDescriptions->count() + 1))->applyFromArray([
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
}
