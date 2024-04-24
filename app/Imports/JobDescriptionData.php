<?php

namespace App\Imports;

use App\Models\JobDescriptionTable;
use App\Models\JobDescriptionSpecializationNeeded;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use App\Models\PublicEntity;
use App\Models\SubEntity;
use App\Models\AffiliatedEntity;
use App\Models\SubAffiliatedEntity;
use App\Models\ScientificCertificateGeneral;
use App\Models\ScientificCertificatePrecise;
use App\Models\Governorate;
use Illuminate\Support\Facades\Auth;

class JobDescriptionData implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $user = Auth::user();

        foreach ($rows as $row) {
            // Trim all fields in the row
            $trimmedRow = $row->map(function ($item) {
                return is_string($item) ? trim($item) : $item;
            });

            // Skip processing if the row is empty
            if ($trimmedRow->filter()->isEmpty()) {
                continue;
            }

            // Extract specializations data
            $specializations = $this->extractSpecializations($trimmedRow);

            // Process and save entities
            $jobDescriptionData = $this->saveEntities($trimmedRow);

            // Map gender value from Arabic to boolean
            $gender = $this->mapGender($trimmedRow['algns_almtlob'] ?? null);

            // Extract job description data
            $jobDescriptionData['gender_needed'] = $gender;
            $jobDescriptionData['record_entry'] = $user->name;
            $jobDescriptionData['last_modifier'] = $user->name;
            // $jobDescriptionData['audited_by'] = $user->name;

            // Create or update the JobDescriptionTable record
            $jobDescriptionId = $this->saveJobDescription($jobDescriptionData);

            // Save specialization needed
            $this->saveSpecializationNeeded($specializations, $jobDescriptionId);
        }
    }

    /**
     * Extract specializations data from the row.
     *
     * @param Collection $row
     * @return array
     */
    private function extractSpecializations(Collection $row)
    {
        $specializations = [];

        for ($i = 1; $i <= 3; $i++) {
            $specializationKey = 'alakhtsas_alaaam_' . $i;
            $specializationPreciseKey = 'alakhtsas_aldkyk_' . $i;

            $specialization = $row[$specializationKey] ?? null;
            $specializationPrecise = $row[$specializationPreciseKey] ?? null;

            if ($specialization !== null) {
                $degreeType = '';
                $category = null;

                if (preg_match('/معهد|معاهد|المعهد/', $specialization)) {
                    $degreeType = 'Intermediate-institute';
                    $category = '2';
                }else{
                    $category = '2';
                }

                $precisesArray = [];

                if ($specializationPrecise !== null && strpos($specializationPrecise, '-') !== false) {
                    $precisesArray = array_map('trim', explode('-', $specializationPrecise));
                    $precisesArray = array_filter($precisesArray);
                } else {
                    $precisesArray = [$specializationPrecise];
                }

                $specializations[] = [
                    'name' => $specialization,
                    'category' => $category,
                    'type' => $degreeType,
                    'precise' => $precisesArray,
                ];
            }
        }

        return $specializations;
    }

    /**
     * Extract and save entities.
     *
     * @param Collection $row
     * @return array
     */
    private function saveEntities(Collection $row)
    {
   // Check and add governorate
    $governorateName = $row['almhafth'];
    if (!empty($governorateName)) {
        $governorate = Governorate::firstOrCreate(['name' => $governorateName]);
        $data['governorate'] = $governorateName;
    }

    // Check and add public entity
    $publicEntityName = $row['alozar'];
    if (!empty($publicEntityName)) {
        $publicEntity = PublicEntity::firstOrCreate(['name' => $publicEntityName]);
        $data['public_entity'] = $publicEntityName;
    }

    // Check and add sub entity
    $subEntityName = $row['algh_alfraay'];
    if (!empty($subEntityName)) {
        $subEntity = SubEntity::firstOrCreate(['name' => $subEntityName, 'public_entity_id' => $publicEntity->id ?? null]);
        $data['sub_entity'] = $subEntityName;
    }

    // Check and add affiliate entity
    $affiliateEntityName = $row['algh_altabaa'];
    if (!empty($affiliateEntityName)) {
        $affiliateEntity = AffiliatedEntity::firstOrCreate(['name' => $affiliateEntityName, 'sub_entity_id' => $subEntity->id ?? null]);
        $data['affiliate_entity'] = $affiliateEntityName;
    }

    // Check and add sub affiliate entity
    $subAffiliateEntityName = $row['algh_altabaa_alfraay'];
    if (!empty($subAffiliateEntityName)) {
        $subAffiliateEntity = SubAffiliatedEntity::firstOrCreate(['name' => $subAffiliateEntityName, 'affiliated_entity_id' => $affiliateEntity->id ?? null]);
        $data['sub_affiliate_entity'] = $subAffiliateEntityName;
    }
        return [
            'governorate' => $governorateName,
            'public_entity' => $publicEntityName,
            'sub_entity' => $subEntityName,
            'affiliate_entity' => $affiliateEntityName,
            'sub_affiliate_entity' => $subAffiliateEntityName,
            'vacancies' => $row['alaadd_almtlob'],
            'job_title' => $row['almsm_alothyfy'],
            'card_number' => $row['rkm_btak_alosf_alothyfy'],
            'category' => '2',
            'work_centers' => intval($row['alaadd_almtlob']),
            'entry_date' => now()->format('Y-m-d'),
            'modification_date' => now()->format('Y-m-d'),
            'status' => 0,
            'assignees' => 0,
        ];
    }

    /**
     * Map gender value from Arabic to boolean.
     *
     * @param string|null $genderValue
     * @return int|null
     */
    private function mapGender(?string $genderValue)
    {
        if ($genderValue === 'زكور' || $genderValue === 'ذكور') {
            return 0;
        } elseif ($genderValue === 'إناث') {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * Create or update the JobDescriptionTable record.
     *
     * @param array $jobDescriptionData
     * @return int
     */
    private function saveJobDescription(array $jobDescriptionData)
    {
        $existingRecord = JobDescriptionTable::where([
            'governorate' => $jobDescriptionData['governorate'],
            'public_entity' => $jobDescriptionData['public_entity'],
            'sub_entity' => $jobDescriptionData['sub_entity'],
            'card_number' => $jobDescriptionData['card_number'],
        ])->first();

        // If there is no existing record, create a new one
        if (!$existingRecord) {
            $jobDescription = JobDescriptionTable::create($jobDescriptionData);
            return $jobDescription->id;
        } else {
            return $existingRecord->id;
        }
    }

    /**
     * Save specialization needed for the job description.
     *
     * @param array $specializations
     * @param int $jobDescriptionId
     * @return void
     */
    private function saveSpecializationNeeded(array $specializations, int $jobDescriptionId)
    {
        foreach ($specializations as $spec) {
            $scientificGeneral = ScientificCertificateGeneral::updateOrCreate(
                ['name' => $spec['name']],
                ['category' => $spec['category'], 'type' => $spec['type']]
            );

            foreach ($spec['precise'] as $precise) {
                if (!empty($precise)) {
                    $scientificPrecise = ScientificCertificatePrecise::updateOrCreate([
                        'name' => $precise,
                        'category' => $spec['category'] ?? null,
                        'certificate_general_id' => $scientificGeneral->id,
                    ]);
                }

                JobDescriptionSpecializationNeeded::updateOrCreate([
                    'job_description_id' => $jobDescriptionId,
                    'degree' => $spec['type'],
                    'specialization_needed' => $scientificGeneral->name,
                    'specialization_needed_precise' => !empty($precise) ? $scientificPrecise->name : null,
                ]);
            }
        }
    }

    /**
     * Get category based on specialization_needed.
     *
     * @param string $specializationNeeded
     * @return int|null
     */
    private function getCategory(string $specializationNeeded)
    {
        if (preg_match('/معهد|معاهد|المعهد/', $specializationNeeded)) {
            return '2';
        }else{
            return '2';
        }

    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
        ];
    }
}
