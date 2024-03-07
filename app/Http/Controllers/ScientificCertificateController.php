<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScientificCertificateGeneral;
use App\Models\ScientificCertificatePrecise;

class ScientificCertificateController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:scientific_certificate_general,name',
            'type' => 'required|string', // Added validation for the 'type' field
            'precise' => 'array',
            'category' => 'required|integer',
        ]);

        // Create ScientificCertificateGeneral
        $certificateGeneral = ScientificCertificateGeneral::create([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'type' => $request->input('type'), // Added the 'type' field
        ]);

        // Create ScientificCertificatePrecise
        foreach ($request->input('precise') as $certificateName) {
            $precise = ScientificCertificatePrecise::create([
                'name' => $certificateName,
                'certificate_general_id' => $certificateGeneral->id,
                'category' => $request->input('category'),
            ]);
        }

        return response()->json([
            'message' => 'ScientificCertificateGeneral and related entities created successfully.',
            'data' => $certificateGeneral,
        ], 201);
    }

    public function getAll()
    {
        $scientificCertificates = ScientificCertificateGeneral::with('scientificCert')->get();

        return response()->json([
            'data' => $scientificCertificates,
        ], 200);
    }

    public function getById($certificate_general_ids)
    {
        $scientificCertificates = ScientificCertificateGeneral::with('scientificCert')->findMany($certificate_general_ids);

        if (!$scientificCertificates) {
            return response()->json([
                'message' => 'ScientificCertificateGeneral not found.',
            ], 404);
        }

        return response()->json([
            'data' => $scientificCertificates,
        ], 200);
    }

    public function delete(Request $request, $certificate_general_ids)
    {
        $request->validate([
            'certificate_precise_ids' => 'array',
        ]);

        // Delete CertificatePrecise
        if ($request->has('certificate_precise_ids')) {
            ScientificCertificatePrecise::whereIn('id', $request->input('certificate_precise_ids'))->delete();
        }

        // Delete ScientificCertificateGeneral and all related entities if no specific ids provided
        if (!$request->hasAny(['certificate_precise_ids'])) {
            ScientificCertificateGeneral::where('id', $certificate_general_ids)->delete();
        }

        return response()->json([
            'message' => 'ScientificCertificateGeneral and related entities deleted successfully.',
        ], 200);
    }

    public function update(Request $request, $certificate_general_ids)
    {
        $request->validate([
            'name' => 'required|string|unique:scientific_certificate_general,name,' . $certificate_general_ids,
            'category' => 'required|integer',
            'precise' => 'array',
        ]);

        // Update ScientificCertificateGeneral
        $certificateGeneral = ScientificCertificateGeneral::find($certificate_general_ids);

        if (!$certificateGeneral) {
            return response()->json([
                'message' => 'Scientific Certificate General not found.',
            ], 404);
        }

        $certificateGeneral->update([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
        ]);

        // Update CertificatePrecise
        $certificateGeneral->scientificCert()->delete();
        foreach ($request->input('precise') as $certificateName) {
            $region = ScientificCertificatePrecise::create([
                'name' => $certificateName,
                'certificate_general_id' => $certificateGeneral->id,
                'category' => $request->input('category'),
            ]);
        }

        return response()->json([
            'message' => 'ScientificCertificateGeneral and related entities updated successfully.',
            'data' => $certificateGeneral,
        ], 200);
    }
}
