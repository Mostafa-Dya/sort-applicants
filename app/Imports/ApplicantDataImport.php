<?php

namespace App\Imports;

use App\Models\Applicant;
use App\Models\DesireData;
use App\Models\SpecializationData;
use App\Models\JobDescriptionTable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ApplicantDataImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $user = Auth::user();

        foreach ($rows as $row) {
            try {
                $this->processRow($row, $user);
            } catch (\Exception $e) {
                // Log or handle the exception as needed
                // For now, we'll just continue to the next row
                continue;
            }
        }
    }

    /**
     * Process a single row of data.
     *
     * @param Collection $row
     * @param mixed $user
     * @return void
     */
    private function processRow(Collection $row, $user)
    {
        // Trim all fields in the row
        $trimmedRow = $row->map(function ($item) {
            return is_string($item) ? trim($item) : $item;
        });

        // Extract unique identifier for the applicant
        $idNumber = $trimmedRow['alrkm_alotny'];

        // Find the applicant by the unique identifier (idNumber)
        $applicant = Applicant::firstOrNew(['idNumber' => $idNumber]);

        // Update or create the applicant
        $this->updateOrCreateApplicant($applicant, $trimmedRow, $user);

        // Process desire and specialization data
        $this->processDesireAndSpecializationData($applicant, $trimmedRow);
    }

    /**
     * Update or create the applicant.
     *
     * @param Applicant $applicant
     * @param Collection $data
     * @param mixed $user
     * @return void
     */
    private function updateOrCreateApplicant(Applicant $applicant, Collection $data, $user)
    {
        $birthDate = Carbon::createFromFormat('d/m/Y', "{$data['yom_almylad']}/{$data['shhr_almylad']}/{$data['sn_tarykh_almylad']}")->toDateString();

        $applicant->fill([
            'fullName' => $data['alasm_althlathy'],
            'gender' => $data['algns'] === 'زكر' ? 0 : ($data['algns'] === 'أنثى' ? 1 : null),
            'motherName' => $data['asm_alam'],
            'birthDate' => $birthDate,
            'governorate' => $data['almhafth'],
            'residence' => $data['mkan_alolad'],
            'graduationDate' => $data['tarykh_altkhrg'],
            'graduationRate' => $data['maadl_altkhrg'],
            'category' => $this->convertCategoryToNumber($data['alfy']),
            'certificate' => json_encode([
                'general' => $data['alakhtsas_alaaam'],
                'precise' => $data['alakhtsas_aldkyk'],
            ]),
            'desiredGovernorate' => $data['almhafth_almrghob'],
            'notes' => $data['mlahthat'],
            'status' => 0,
            'accepted' => 0,
            'entryDate' => now()->format('Y-m-d'),
            'modificationDate' => now()->format('Y-m-d'),
            'recordEntry' => $user->name,
            'lastModifier' => $user->name,
        ])->save();
    }

    /**
     * Process desire and specialization data.
     *
     * @param Applicant $applicant
     * @param Collection $data
     * @return void
     */
    private function processDesireAndSpecializationData(Applicant $applicant, Collection $data)
    {
        // Process up to 3 desire data
        for ($index = 1; $index <= 3; $index++) {
            $mhafthKey = "almhafth_almrghob";
            $alghKey = "alozar_$index";
            $rkmKey = "rkm_btak_alosf_$index";

            if ($data[$mhafthKey] && $data[$alghKey] && $data[$rkmKey]) {
                $jobDescription = JobDescriptionTable::where([
                    ['governorate', '=', $data[$mhafthKey]],
                    ['public_entity', '=', $data[$alghKey]],
                    ['card_number', '=', $data[$rkmKey]],
                ])->first();

                if ($jobDescription) {
                    $desireData = [
                        'governorateDesire' => $data[$mhafthKey],
                        'publicEntitySide' => $data[$alghKey],
                        'cardNumberDesire' => $data[$rkmKey],
                    ];

                    $this->saveDesireAndSpecializationData($applicant, $desireData, $jobDescription);
                }
            }
        }
    }

    /**
     * Save desire and specialization data.
     *
     * @param Applicant $applicant
     * @param array $desireData
     * @param JobDescriptionTable $jobDescription
     * @return void
     */
    private function saveDesireAndSpecializationData(Applicant $applicant, array $desireData, JobDescriptionTable $jobDescription)
    {
        $desire = $applicant->desireData()->create([
            'governorateDesire' => $desireData['governorateDesire'],
            'publicEntitySide' => $desireData['publicEntitySide'],
            'cardNumberDesire' => $desireData['cardNumberDesire'],
        ]);

        $specialization = $applicant->specializationData()->create([
            'desire' => $jobDescription->public_entity,
            'namedVal' => $jobDescription->job_title,
            'cardNumberVal' => $jobDescription->card_number,
        ]);
    }

    /**
     * Convert category string to number.
     *
     * @param string $category
     * @return int|null
     */
    private function convertCategoryToNumber($category)
    {
        $categoryMap = [
            'الاولى' => '1',
            'الثانية' => '2',
            'الثالثة' => '3',
            'الرابعة' => '4',
            'الخامسة' => '5',
        ];

        return $categoryMap[$category] ?? null;
    }

    /**
     * Return CSV custom settings.
     *
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
        ];
    }
}
