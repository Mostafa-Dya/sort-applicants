<?php

namespace App\Exports;

use App\Models\Applicant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use App\Models\JobDescriptionTable;

class ApplicantDataExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithCustomCsvSettings
{
    use Exportable;

    protected $applicants;

    public function __construct(Collection $applicants)
    {
        $this->applicants = $applicants;
    }

    public function collection()
    {
        // Filter applicants with accepted equal to 1
        $acceptedApplicants = $this->applicants->filter(function ($applicant) {
            return $applicant->accepted == 1;
        });
    
        // Transform the filtered applicant data before exporting
        $formattedApplicants = $acceptedApplicants->map(function ($applicant) {
            $certificateData = json_decode($applicant->certificate, true);
            $general = isset($certificateData['general']) ? $certificateData['general'] : '';
            $precise = isset($certificateData['precise']) ? $certificateData['precise'] : '';
            // Initialize an array to store the applicant's data
            $certificateData = json_decode($applicant->certificate, true);
            $gender = ($applicant->gender == 1) ? 'أنثى' : 'ذكر';
    
            $rowData = [
                'الاسم الثلاثي' => $applicant->fullName,
                'الجنس' => $gender,
                'اسم الأم' => $applicant->motherName,
                'الرقم الوطني' => $applicant->idNumber,
                'تاريخ الميلاد' => $applicant->birthDate,
    
                'المؤهل العلمي' => $general . ' (' . $precise . ')',
    
                'معدل التخرج' => $applicant->graduationRate,
                'المحافظة المفرز لها' => $applicant->desiredGovernorate,
            ];
    
            $jobDescription = JobDescriptionTable::where('governorate', $applicant->desiredGovernorate)
                ->where('job_title', $applicant->named)
                ->where('card_number', $applicant->cardNumber)
                ->first();
    
            if ($jobDescription) {
                // Add job description data to the row
                $rowData['الوزارة المفرز لها'] = $jobDescription->public_entity;
                $rowData['الجهة الفرعية'] = $jobDescription->sub_entity;
                $rowData['الجهة التابعة'] = $jobDescription->affiliate_entity;
                $rowData['الجهة التابعة الفرعية'] = $jobDescription->sub_affiliate_entity;
                $rowData['المسمى الوظيفي'] = $applicant->named;
                $rowData['رقم بطاقة الوصف'] = $applicant->cardNumber;
    
            } else {
                // Add empty values if job description data doesn't exist
                $rowData['الوزارة المفرز لها'] = '-';
                $rowData['الجهة الفرعية'] = '-';
                $rowData['الجهة التابعة'] = '-';
                $rowData['الجهة التابعة الفرعية'] = '-';
            }
    
            // Fetch desire data for the applicant
    
    
            return $rowData;
        });
    
        return $formattedApplicants;
    }
    
    

    public function headings(): array
    {
        return [
            'الاسم الثلاثي',
            'الجنس',
            'اسم الأم',
            'الرقم الوطني',
            'تاريخ الميلاد',
            'المؤهل العلمي',
            'معدل التخرج',
            'المحافظة المفرز لها',
            'الوزارة المفرز لها',
            'الجهة الفرعية',
            'الجهة التابعة',
            'الجهة التابعة الفرعية',
            'المسمى الوظيفي',
            'رقم بطاقة الوصف'

        ];
    }
    

    public function styles($sheet)
    {
        // Apply styling to the table headers
        $sheet->getStyle('A1:N1')->applyFromArray([
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
        $sheet->getStyle('A2:N1000')->applyFromArray([
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
