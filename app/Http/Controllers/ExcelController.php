<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Governorate;
use App\Models\JobDescriptionTable;
use App\Models\Applicant;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GovernorateExport;
use App\Exports\JobDescriptionTableExport;
use App\Exports\JobDescriptionHeadersExport;
use App\Exports\ApplicantDataExport;
use App\Exports\ApplicantHeadersExport;

use App\Imports\ApplicantDataImport;
use App\Imports\JobDescriptionData;


class ExcelController extends Controller
{
    public function exportGovernorateData()
    {
        return Excel::download(new GovernorateExport, 'governorate_data.xlsx');
    }

    public function exportJobDescriptionData()
    {
        // Retrieve all job descriptions
        $jobDescriptions = JobDescriptionTable::all();

        // Define the file name for the exported Excel file
        $fileName = 'job_descriptions_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        // Generate Excel file using JobDescriptionTableExport class
        return Excel::download(new JobDescriptionTableExport($jobDescriptions), $fileName);
    }

    public function exportApplicantsData(Request $request)
    {
        // Retrieve search fields from the request
        $searchFields = $request->all(); // Adjust this according to your request structure
    
        // Build the query to filter applicants based on search fields
        $query = Applicant::query();
    
        // Apply filters based on search fields
        if (!empty($searchFields)) {
            // Example: If 'category' is a search field
            if (isset($searchFields['category'])) {
                $query->where('category', $searchFields['category']);
            }
    
            // Add more conditions for other search fields as needed
        }
    
        // Retrieve filtered applicants
        $applicants = $query->get();
    
        // Define the file name for the exported Excel file
        $fileName = 'Applicant_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    
        // Generate Excel file using ApplicantDataExport class
        return Excel::download(new ApplicantDataExport($applicants), $fileName);
    }

    public function exportJobDescriptionHeaders()
{
    // Generate Excel file using JobDescriptionHeadersExport class
    return Excel::download(new JobDescriptionHeadersExport, 'job_description_headers.xlsx');
}

public function exportApplicantsHeaders()
{
    // Generate Excel file using JobDescriptionHeadersExport class
    return Excel::download(new ApplicantHeadersExport, 'applicants_headers.xlsx');
}


public function importJobDescriptionData(Request $request)
{
    // Validate the request
    $request->validate([
        'file' => 'required|mimes:xlsx,xls|max:10000000000000000240', // Adjust max file size as needed
    ]);

    // Retrieve the file from the request
    $file = $request->file('file');

    try {
        // Import data from Excel file
        Excel::import(new JobDescriptionData, $file);

        // Return a success response
        return response()->json(['message' => 'Job description data imported successfully'], 200);
    } catch (\Exception $e) {
        // Return an error response if import fails
        return response()->json(['message' => 'Error importing job description data', 'error' => $e->getMessage()], 500);
    }
}

public function importApplicantsData(Request $request)
{
    // Validate the request
    $request->validate([
        'file' => 'required|mimes:xlsx,xls|max:10240', // Adjust max file size as needed
    ]);

    // Retrieve the file from the request
    $file = $request->file('file');

    try {
        // Import data from Excel file
        Excel::import(new ApplicantDataImport, $file);

        // Return a success response
        return response()->json(['message' => 'Applicants data imported successfully'], 200);
    } catch (\Exception $e) {
        // Return an error response if import fails
        return response()->json(['message' => 'Error importing Applicants data', 'error' => $e->getMessage()], 500);
    }
}


    
}
