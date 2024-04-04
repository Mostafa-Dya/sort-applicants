<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $query = Applicant::with('desireData','specializationData'); 

        // Check if any search parameters are provided
        if (!empty($request->all())) {
            // Search conditions for numeric fields
            $this->applyNumericOrStringSearchCondition($query, 'idNumber', $request->input('idNumber'));
            $this->applyNumericSearchCondition($query, 'exam_result', $request->input('exam_result'));
            $this->applyNumericSearchCondition($query, 'cardNumber', $request->input('cardNumber'));
            $this->applyNumericSearchCondition($query, 'order_of_desire', $request->input('order_of_desire'));
            $this->applyNumericSearchCondition($query, 'graduationRate', $request->input('graduationRate'));

            
            
            $this->applyStringSearchCondition($query, 'category', $request->input('category'));
            $this->applyStringSearchCondition($query, 'governorate', $request->input('governorate'));
            $this->applyStringSearchCondition($query, 'destination', $request->input('destination'));
            $this->applyStringSearchCondition($query, 'entity', $request->input('entity'));
            $this->applyStringSearchCondition($query, 'fullName', $request->input('fullName'));
            $this->applyStringSearchCondition($query, 'does_not_match', $request->input('does_not_match'));
            $this->applyStringSearchCondition($query, 'recordEntry', $request->input('recordEntry'));
            $this->applyStringSearchCondition($query, 'entry_date', $request->input('entry_date'));
            $this->applyStringSearchCondition($query, 'lastModifier', $request->input('lastModifier'));
            $this->applyStringSearchCondition($query, 'modified_date', $request->input('modified_date'));
            // Add more conditions as needed


            $this->applyStringSearchCondition($query,'certificate', $request->input('certificate'));

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
                        $min = number_format($numericValue['min'], 3, '.', '');
                        $max = number_format($numericValue['max'], 3, '.', '');
                        $query->whereBetween($field, [$min, $max]);
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

    // Helper method to apply numeric or string search conditions
private function applyNumericOrStringSearchCondition($query, $field, $value)
{
    if ($value !== null) {
        $condition = $value['condition'] ?? 'equals';
        $stringValue = $value['value'] ?? null;

        switch ($condition) {
            case 'equals':
                $query->where($field, '=', $stringValue);
                break;
            case 'greater_than':
                $query->where($field, '>', $stringValue);
                break;
            case 'less_than':
                $query->where($field, '<', $stringValue);
                break;
            case 'greater_than_or_equal':
                $query->where($field, '>=', $stringValue);
                break;
            case 'less_than_or_equal':
                $query->where($field, '<=', $stringValue);
                break;
            case 'range':
                $minValue = isset($stringValue['min']) ? $stringValue['min'] : null;
                $maxValue = isset($stringValue['max']) ? $stringValue['max'] : null;
                if ($minValue !== null && $maxValue !== null) {
                    $query->whereBetween($field, [$minValue, $maxValue]);
                }
                break;
            // Add more conditions as needed
        }
    }
}


    
    
    
    
    
    
    
    
    
    
    
    
    

    public function getAll()
    {
        $applicants = Applicant::with('desireData', 'specializationData')->get();
    
        return response()->json($applicants, 200);
    }
    

    public function show($id)
    {
        $applicants = Applicant::with('desireData', 'specializationData')->find($id);
        return response()->json($applicants);
    }

    public function destroy($id)
    {
        Applicant::destroy($id);
        return response()->json(['message' => 'Record deleted successfully']);
    }

    public function deleteApplicants(Request $request)
    {
        // Validate the incoming request data
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:applicants,id', // Ensure all IDs exist in the database
        ]);

        // Extract the IDs from the request
        $ids = $request->input('ids');

        // Delete the applicants with the given IDs
        Applicant::whereIn('id', $ids)->delete();

        // Return success message
        return response()->json(['message' => 'Applicants deleted successfully'], 200);
    }


    public function store(Request $request)
    {
        // Validate the incoming request data
        $this->validate($request, [
            'birthDate' => 'required|date',
            'cardNumber' => 'nullable|numeric',
            'category' => 'required|string',
            'certificate' => 'required|array',
            'certificate.general' => 'required|string',
            'certificate.precise' => 'required|string',
            'desiredGovernorate' => 'required|string',
            'destination' => 'nullable|string',
            'desireOrder' => 'nullable|string',
            'exactSpecialization' => 'nullable|string',
            'fullName' => 'required|string',
            'governorate' => 'required|string',
            'graduationDate' => 'required|string',
            'graduationRate' => 'required|numeric|between:0,9999999.999',
            'idNumber' => 'required|string',
            'institute' => 'nullable|string',
            'motherName' => 'required|string',
            'residence' => 'nullable|string',
            'notes' => 'nullable|string',
            'named' => 'nullable|string',
            // 'series' => 'nullable|integer',
            'lastModifier' => 'nullable|string',
            'modificationDate' => 'nullable|date',
            'recordEntry' => 'nullable|string',
            'entryDate' => 'nullable|date',
            'status' => 'boolean',
            'accepted' => 'boolean',
            'exam_result' => 'nullable|numeric',
            'the_ministry'=>'nullable|string',
            'sub_entity'=>'nullable|string',
            'gender'=>'required|boolean'
        ]);
    
        // Manually check and decode the 'certificate' field
        $certificate = $request->input('certificate');
        if (!is_array($certificate) || !array_key_exists('general', $certificate) || !array_key_exists('precise', $certificate)) {
            return response()->json(['error' => 'Invalid certificate format.'], 422);
        }
    
        // Create an array with the decoded certificate
        $certificateData = [
            'general' => $certificate['general'],
            'precise' => $certificate['precise'],
        ];
    
        // Encode the 'certificate' data as a JSON string
        $certificateJson = json_encode($certificateData);
        $graduationRate = number_format($request->input('graduationRate'), 3, '.', '');

        // Continue with the rest of the request data
        $requestData = [
            'governorate' => $request->input('governorate'),
            'category' => $request->input('category'),
            // 'series' => $request->input('series'),
            'fullName' => $request->input('fullName'),
            'motherName' => $request->input('motherName'),
            'idNumber' => $request->input('idNumber'),
            'graduationDate' => $request->input('graduationDate'),
            'graduationRate' => $graduationRate,
            'birthDate' => $request->input('birthDate'),
            'residence' => $request->input('residence'),
            'institute' => $request->input('institute'),
            'exactSpecialization' => $request->input('exactSpecialization'),
            'certificate' => $certificateJson,
            'notes' => $request->input('notes'),
            'destination' => $request->input('destination'),
            'named' => $request->input('named'),
            'cardNumber' => $request->input('cardNumber'),
            'desireOrder' => $request->input('desireOrder'),
            'desiredGovernorate' => $request->input('desiredGovernorate'),
            'desireData' => $request->input('desireData'),
            'specializationData' => $request->input('specializationData'),
            'recordEntry' => $request->input('recordEntry'),
            'entryDate' => $request->input('entryDate'),
            'lastModifier' => $request->input('lastModifier'),
            'modificationDate' => $request->input('modificationDate'),
            'the_ministry' => $request->input('the_ministry'),
            'sub_entity' => $request->input('sub_entity'),
            'gender' => $request->input('gender'),
        ];
    
        // Create a new record
        $newRecord = Applicant::create($requestData);
    
        // Create related DesireData
        foreach ($request->input('desireData') as $desireData) {
            $newRecord->desireData()->create($desireData);
        }
    
        // Create related SpecializationData
        foreach ($request->input('specializationData') as $specializationData) {
            $newRecord->specializationData()->create([
                'desire' => $specializationData['desire'],
                'namedVal' => $specializationData['namedVal'],

                'cardNumberVal' => $specializationData['cardNumberVal'],
            ]);
        }
    
        // Return the newly created record
        return response()->json($newRecord, 201);
    }
    
    
    public function update(Request $request, $applicantId)
    {
        // Validate the incoming request data
        $this->validate($request, [
            'graduationRate' => 'nullable|numeric|between:0,9999999.999',
        ]);
    
        // Find the applicant by ID
        $applicant = Applicant::findOrFail($applicantId);
    
        // Update the fields based on the provided data
        $applicant->update($request->all());
    
        // Update related DesireData
        if ($request->has('desireData')) {
            $applicant->desireData()->delete(); // Delete existing DesireData
            foreach ($request->input('desireData') as $desireData) {
                $applicant->desireData()->create($desireData);
            }
        }
    
        // Update related SpecializationData
        if ($request->has('specializationData')) {
            $applicant->specializationData()->delete(); // Delete existing SpecializationData
            foreach ($request->input('specializationData') as $specializationData) {
                $applicant->specializationData()->create([
                    'desire' => $specializationData['desire'],
                    'namedVal' => $specializationData['namedVal'],
                    'cardNumberVal' => $specializationData['cardNumberVal'],
                ]);
            }
        }
    
        // Return the updated applicant
        return response()->json($applicant, 200);
    }
    
   

    
}
