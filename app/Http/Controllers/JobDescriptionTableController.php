<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobDescriptionTable;
use App\Models\Applicant;
use App\Models\JobDescriptionSpecializationNeeded;
use Illuminate\Support\Facades\DB;


class JobDescriptionTableController extends Controller
{
    public function index(Request $request)
    {
        $query = JobDescriptionTable::with('specializationNeeded'); 
    
        // Check if any search parameters are provided
        if (!empty($request->all())) {
            // Search conditions for numeric fields
            $this->applyNumericSearchCondition($query, 'id', $request->input('id'));
            $this->applyNumericSearchCondition($query, 'work_centers', $request->input('work_centers'));
            $this->applyNumericSearchCondition($query, 'assignees', $request->input('assignees'));
            $this->applyNumericSearchCondition($query, 'vacancies', $request->input('vacancies'));
            $this->applyNumericSearchCondition($query, 'card_number', $request->input('card_number'));

            // Search conditions for string fields
            $this->applyStringSearchCondition($query, 'category', $request->input('category'));
            $this->applyStringSearchCondition($query, 'governorate', $request->input('governorate'));

            $this->applyStringSearchCondition($query, 'status', $request->input('status'));
            $this->applyStringSearchCondition($query, 'public_entity', $request->input('public_entity'));
            $this->applyStringSearchCondition($query, 'sub_entity', $request->input('sub_entity'));
            $this->applyStringSearchCondition($query, 'job_title', $request->input('job_title'));
            $this->applyStringSearchCondition($query, 'specialization', $request->input('specialization'));
            $this->applyStringSearchCondition($query, 'record_entry', $request->input('record_entry'));
            $this->applyStringSearchCondition($query, 'last_modifier', $request->input('last_modifier'));
            $this->applyStringSearchCondition($query, 'audited_by', $request->input('audited_by'));
            // Add more string fields as needed
        }
    
        $data = $query->get();
    
        return response()->json($data);
    }
    

    // Helper method to apply numeric search conditions
    private function applyNumericSearchCondition($query, $field, $value)
    {
        if ($value !== null) {
            $condition = $value['condition'] ?? 'equals';
            $numericValue = $value['value'] ?? null;

            switch ($condition) {
                case 'equals':
                    $query->where($field, '=', $numericValue);
                    break;
                case 'greater_than':
                    $query->where($field, '>', $numericValue);
                    break;
                case 'less_than':
                    $query->where($field, '<', $numericValue);
                    break;
                case 'greater_than_or_equal':
                    $query->where($field, '>=', $numericValue);
                    break;
                case 'less_than_or_equal':
                    $query->where($field, '<=', $numericValue);
                    break;
                case 'range':
                    $query->whereBetween($field, [$numericValue['min'], $numericValue['max']]);
                    break;
                // Add more conditions as needed
            }
        }
    }

    // Helper method to apply string search conditions
    private function applyStringSearchCondition($query, $field, $value)
    {
        if ($value !== null) {
            // Check if the field is governorate
            if ($field === 'governorate') {
                $query->where(function ($query) use ($field, $value) {
                    $query->where($field, $value)
                        ->orWhere($field, 'like', $value . ' -%'); 
                });
            } else {
                // Apply the default like condition for other fields
                $query->where($field, 'like', '%' . $value . '%');
            }
        }
    }

    public function show($id)
    {
        $row = JobDescriptionTable::with('specializationNeeded')->find($id);

        return response()->json($row);
    }

    public function destroySolo($id)
    {
        // Delete related records in job_description_specialization_needed
        JobDescriptionSpecializationNeeded::where('job_description_id', $id)->delete();
    
        // Delete the main record
        JobDescriptionTable::destroy($id);
    
        return response()->json(['message' => 'Record deleted successfully']);
    }


    public function destroy(Request $request)
{
    $ids = $request->input('ids');

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['error' => 'No valid IDs provided'], 400);
    }

    // Delete related records in job_description_specialization_needed for each ID
    foreach ($ids as $id) {
        JobDescriptionSpecializationNeeded::where('job_description_id', $id)->delete();
        // Delete the main record
        JobDescriptionTable::destroy($id);
    }

    return response()->json(['message' => 'Records deleted successfully']);
}



    public function store(Request $request)
    {
        // Validate the incoming request data
        $this->validate($request, [
            'status' => 'boolean',
            'category' => 'required|string',
            'public_entity' => 'required|string',
            'sub_entity' => 'nullable|string',
            'governorate' => 'required|string',
            'job_title' => 'required|string',
            'general' => 'nullable|string',
            'assignees' => 'required|integer',
            'vacancies' => 'required|integer',
            'card_number' => 'required|integer',
            'record_entry' => 'nullable|string',
            'entry_date' => 'nullable|date',
            'last_modifier' => 'nullable|string',
            'modification_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'audited_by' => 'nullable|string',
            'specializationData' => 'required|array',

            'affiliate_entity' => 'nullable|string',
            'sub_affiliate_entity' => 'nullable|string',
            'gender_needed' => 'integer',

        ]);

        $work_centers = $request->input('vacancies') - $request->input('assignees');

    
        // Create a new record in job_description_tables
        $newRecord = JobDescriptionTable::create([
            'status' => $request->input('status'),
            'general' => $request->input('general'),
            'precise' => $request->input('precise'),
            'category' => $request->input('category'),
            'public_entity' => $request->input('public_entity'),
            'sub_entity' => $request->input('sub_entity'),
            'governorate' => $request->input('governorate'),

            'affiliate_entity' => $request->input('affiliate_entity'),
            'sub_affiliate_entity' => $request->input('sub_affiliate_entity'),
            'gender_needed' => $request->input('gender_needed'),

            'job_title' => $request->input('job_title'),
            'assignees' => $request->input('assignees'),
            'vacancies' => $request->input('vacancies'),
            'card_number' => $request->input('card_number'),
            'record_entry' => $request->input('record_entry'),
            'entry_date' => $request->input('entry_date'),
            'last_modifier' => $request->input('last_modifier'),
            'modification_date' => $request->input('modification_date'),
            'audited_by' => $request->input('audited_by'),
            'notes' => $request->input('notes'),
            'work_centers'=>$work_centers,
            
        ]);
    
        // Associate the specializationData with the new job_description_tables record
        foreach ($request->input('specializationData') as $specializationData) {
            JobDescriptionSpecializationNeeded::create([
                'job_description_id' => $newRecord->id,
                'degree' => $specializationData['degree'],
                'specialization_needed' => $specializationData['specializationNeeded'],
                'specialization_needed_precise' => $specializationData['specializationNeededPrecise'],
            ]);
        }
    
        // Return the newly created record
        return response()->json($newRecord, 201);
    }
    
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $this->validate($request, [
            'status' => 'boolean',
            'category' => 'nullable|string',
            'public_entity' => 'nullable|string',
            'sub_entity' => 'nullable|string',
            'governorate' => 'nullable|string',
            'job_title' => 'nullable|string',
            'general' => 'nullable|string',
            'assignees' => 'nullable|integer',
            'vacancies' => 'nullable|integer',
            'card_number' => 'nullable|integer',
            'record_entry' => 'nullable|string',
            'entry_date' => 'nullable|date',
            'last_modifier' => 'nullable|string',
            'modification_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'audited_by' => 'nullable|string',
            'specializationData' => 'nullable|array',

            'affiliate_entity' => 'nullable|string',
            'sub_affiliate_entity' => 'nullable|string',
            'gender_needed' => 'integer',
        ]);

        $work_centers = $request->input('vacancies') - $request->input('assignees');

        // Find the existing record in job_description_tables
        $existingRecord = JobDescriptionTable::findOrFail($id);
    
        // Update the existing record with the new data
        $existingRecord->update([
            'status' => $request->input('status'),
            'general' => $request->input('general'),
            'precise' => $request->input('precise'),
            'category' => $request->input('category'),
            'public_entity' => $request->input('public_entity'),
            'sub_entity' => $request->input('sub_entity'),

            'affiliate_entity' => $request->input('affiliate_entity'),
            'sub_affiliate_entity' => $request->input('sub_affiliate_entity'),
            'gender_needed' => $request->input('gender_needed'),

            'governorate' => $request->input('governorate'),
            'job_title' => $request->input('job_title'),
            'assignees' => $request->input('assignees'),
            'vacancies' => $request->input('vacancies'),
            'card_number' => $request->input('card_number'),
            'record_entry' => $request->input('record_entry'),
            'entry_date' => $request->input('entry_date'),
            'last_modifier' => $request->input('last_modifier'),
            'modification_date' => $request->input('modification_date'),
            'audited_by' => $request->input('audited_by'),
            'notes' => $request->input('notes'),
            'work_centers'=>$work_centers
        ]);
        // Update existing associated specializationData
        $existingSpecializations = JobDescriptionSpecializationNeeded::where('job_description_id', $existingRecord->id)->get();

        // Iterate over the updated specialization data provided in the request
        foreach ($request->input('specializationData') as $data) {
            // Check if there is an existing record with the same specialization_needed_precise
            $existingSpecialization = $existingSpecializations->where('specialization_needed_precise', $data['specializationNeededPrecise'])->first();
    
            if ($existingSpecialization) {
                // Update the existing specialization record
                $existingSpecialization->update([
                    'degree' => $data['degree'],
                    'specialization_needed' => $data['specializationNeeded'],
                    'specialization_needed_precise' => $data['specializationNeededPrecise'],
                ]);
    
                // Remove the updated specialization from the existing specialization list
                $existingSpecializations = $existingSpecializations->reject(function ($existing) use ($existingSpecialization) {
                    return $existing->id === $existingSpecialization->id;
                });
            } else {
                // Create new specialization data
                JobDescriptionSpecializationNeeded::create([
                    'job_description_id' => $existingRecord->id,
                    'degree' => $data['degree'],
                    'specialization_needed' => $data['specializationNeeded'],
                    'specialization_needed_precise' => $data['specializationNeededPrecise'],
                ]);
            }
        }
    
        // Delete any remaining existing specialization data records (no longer present in the updated request)
        $existingSpecializations->each->delete();
    
        // Fetch the updated record with associated specialization data
        $updatedRecord = JobDescriptionTable::with('specializationNeeded')->find($id);

        // Return the updated record
        return response()->json($updatedRecord, 200);
    }
    
        
    

    public function getJobDescriptionsWithApplicants()
    {
        // Retrieve all job descriptions
        $jobDescriptions = JobDescriptionTable::all();

        // Loop through each job description and retrieve associated applicants
        foreach ($jobDescriptions as $jobDescription) {
            $applicants = Applicant::whereHas('desireData', function ($query) use ($jobDescription) {
                $query->where([
                    ['governorateDesire', '=', $jobDescription->governorate],
                    ['publicEntitySide', '=', $jobDescription->public_entity],
                    ['cardNumberDesire', '=', $jobDescription->card_number],
                ]);
            })->with('desireData', 'specializationData')->get();
            

            // Add the array of applicants to the job description object
            $jobDescription->applicants = $applicants;
        }

        return response()->json([
            'data' => $jobDescriptions,
        ], 200);
    }



    
    public function acceptApplicants(Request $request)
    {
        // Retrieve all applicants
        $applicants = Applicant::all();
    
        // Track accepted applicants
        $acceptedApplicants = [];
    
        // Iterate through each applicant
        foreach ($applicants as $applicant) {
            $desireData = $applicant->desireData->first(); // Assuming desireData is a collection
            if (!$desireData) {
                continue;
            }
            // Retrieve job description for this applicant
            $jobDescription = JobDescriptionTable::with('specializationNeeded')->where([
                ['governorate', '=', $desireData->governorateDesire],
                ['public_entity', '=', $desireData->publicEntitySide],
                ['card_number', '=', $desireData->cardNumberDesire],
            ])->first();
            
    
            // If no job description found or vacancies are already filled or job description status is 0, skip to the next applicant
            if (!$jobDescription || $jobDescription->assignees >= $jobDescription->vacancies || $jobDescription->status == 0) {
                continue;
            }
    
            // If the job description is gender-specific and does not match the applicant's gender, skip to the next applicant
            if ($jobDescription->gender_needed === 0 || $jobDescription->gender_needed === 1) {
                if ($jobDescription->gender_needed !== $applicant->gender) {
                    continue;
                }
            }

            $certificate = json_decode($applicant->certificate, true);
        
            // Check if the applicant's certificate matches any specialization needed for this job description
            $certificateMatches = false;
            $specialization_needed = $jobDescription->specializationNeeded;
            foreach ($specialization_needed as $specialization) {
                // Check if precise specialization is not null or empty
                if (!empty($specialization['specialization_needed_precise'])) {
                    // Compare both general and precise specializations
                    if ($certificate['general'] === $specialization['specialization_needed'] && $certificate['precise'] === $specialization['specialization_needed_precise']) {
                        $certificateMatches = true;
                        break;
                    }
                } else {
                    // Compare only general specialization
                    if ($certificate['general'] === $specialization['specialization_needed']) {
                        $certificateMatches = true;
                        break;
                    }
                }
            }
        
            
            // If the applicant's certificate does not match any specialization needed for this job description, skip to the next applicant
            if (!$certificateMatches) {
                continue;
            }
            
            

    
            // Sort all applicants based on graduation rate, then birthdate
            $applicants = $applicants->sortBy(function ($applicant) {
                // Split the graduation rate into integer and fractional parts
                $parts = explode('.', $applicant->graduationRate);
    
                // Compare the first two digits after the comma
                $firstTwoDigits = $parts[0] . '.' . substr($parts[1], 0, 2);
    
                // Compare the first three digits after the comma if available
                $nextThreeDigits = isset($parts[1][2]) ? substr($parts[1], 0, 3) : '';
    
                // Combine for comparison
                $combined = $firstTwoDigits;
    
                // Check if other applicants have the same graduation rate (2 numbers after comma)
                if ($applicant->graduationRate == $firstTwoDigits) {
                    // All applicants have the same graduation rate (2 numbers after comma), check the next condition
                    $combined .= $nextThreeDigits;
                } else {
                    // Sort by graduation rate with three digits after the comma if available
                    $combined .= $nextThreeDigits;
    
                    // Check if other applicants have the same graduation rate (3 numbers after comma)
                    if ($applicant->graduationRate == ($firstTwoDigits . $nextThreeDigits)) {
                        // All applicants have the same graduation rate (3 numbers after comma), sort by birth date
                        $combined .= $applicant->birthDate;
                    }
                }
    
                return $combined;
            });
    
            // If there are still vacancies available and both job description status and applicant status are 1, accept the applicant
            if ($jobDescription->assignees < $jobDescription->vacancies && $applicant->status == 1 && $jobDescription->status == 1) {
                // Update applicant details
                $applicant->destination = $jobDescription->public_entity;
                $applicant->named = $jobDescription->job_title;
                $applicant->cardNumber = $jobDescription->card_number;
                $applicant->accepted = true;
                $applicant->sub_entity = $jobDescription->sub_entity;
    
                // Find the index of the accepted job description within the desireData array
                $desireOrderIndex = 0;
                foreach ($applicant->desireData as $index => $desire) {
                    // Convert cardNumberDesire to integer for comparison
                    $desireCardNumber = intval($desire->cardNumberDesire);
    
                    // Check if the converted cardNumberDesire matches the card_number of the current job description
                    if ($desireCardNumber === $jobDescription->card_number) {
                        // Set the desireOrderIndex to the current index
                        $desireOrderIndex = $index + 1; // Adding 1 to start from 1 instead of 0
                        break;
                    }
                }
    
                // Set the desireOrder for the applicant
                $applicant->desireOrder = $desireOrderIndex;
    
                // Save the applicant
                $applicant->save();
    
                // Update job description table
                $jobDescription->assignees++;
                $jobDescription->save();
    
                // Add the applicant to the accepted applicants list
                $acceptedApplicants[$applicant->id] = true;
            } else {
                // Reject the applicant and provide a reason based on different conditions
                if (!isset($acceptedApplicants[$applicant->id])) {
                    $rejectionReason = '';
    
                    // Check for low graduation rate
                    if ($applicant->graduationRate < $jobDescription->required_graduation_rate) {
                        $rejectionReason = 'Low graduation rate.';
                    }
    
                    // Check for age requirement
                    if ($applicant->birthDate < $jobDescription->minimum_birth_date) {
                        $rejectionReason = 'Age requirement not met.';
                    }
    
                    // Check for other requirements
                    if (!$rejectionReason) {
                        $rejectionReason = 'Does not meet requirements.';
                    }
    
                    // Set rejection reason for the applicant
                    $applicant->reason = $rejectionReason;
                    $applicant->save();
                }
            }
        }
    
        return response()->json([
            'message' => 'Applicants accepted successfully.',
        ], 200);
    }
    

    
    
    public function revertAcceptedApplicants(Request $request)
    {
        // Retrieve all job descriptions
        $jobDescriptions = JobDescriptionTable::all();
    
        // Loop through each job description
        foreach ($jobDescriptions as $jobDescription) {
            // Retrieve accepted applicants for this job description
            $acceptedApplicants = Applicant::where('accepted', true)
                ->where('destination', $jobDescription->public_entity)
                ->where('named', $jobDescription->job_title)
                ->where('cardNumber', $jobDescription->card_number)
                ->get();
    
            // Revert changes for each accepted applicant
            foreach ($acceptedApplicants as $applicant) {
                // Revert applicant details
                $applicant->accepted = false;
                $applicant->destination = null;
                $applicant->named = null;
                $applicant->cardNumber = null;
                $applicant->sub_entity = null;
                $applicant->desireOrder = null;
                $applicant->save();
            }
    
            // Set the count of assignees to 0
            DB::table('job_description_tables')
                ->where('id', $jobDescription->id)
                ->update(['assignees' => 0]);
        }
    
        return response()->json([
            'message' => 'Changes reverted successfully.',
        ], 200);
    }
    
    
    
    
    
    
}
