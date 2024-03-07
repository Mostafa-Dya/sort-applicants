<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Governorate;
use App\Models\Region;
use App\Models\Township;
use App\Models\Village;

class GovernorateController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:governorates,name',
            'regions' => 'array',
            'townships' => 'array',
            'villages' => 'array',
        ]);

        // Create Governorate
        $governorate = Governorate::create([
            'name' => $request->input('name'),
        ]);

        // Create Regions
        foreach ($request->input('regions') as $regionName) {
            $region = Region::create([
                'name' => $regionName,
                'governorate_id' => $governorate->id,
            ]);
        }

        // Create Townships
        foreach ($request->input('townships') as $townshipName) {
            $township = Township::create([
                'name' => $townshipName,
                'governorate_id' => $governorate->id,
            ]);
        }

        // Create Villages
        foreach ($request->input('villages') as $villageName) {
            Village::create([
                'name' => $villageName,
                'governorate_id' => $governorate->id,
            ]);
        }

        return response()->json([
            'message' => 'Governorate and related entities created successfully.',
            'data' => $governorate,
        ], 201);
    }
    public function getAll()
    {
        $governorates = Governorate::with('regions', 'townships', 'villages')->get();

        return response()->json([
            'data' => $governorates,
        ], 200);
    }
    public function getById($governorateId)
    {
        $governorate = Governorate::with('regions', 'townships', 'villages')->find($governorateId);
    
        if (!$governorate) {
            return response()->json([
                'message' => 'Governorate not found.',
            ], 404);
        }
    
        return response()->json([
            'data' => $governorate,
        ], 200);
    }
    
    public function delete(Request $request, $governorateId)
    {
        $request->validate([
            'region_ids' => 'array',
            'township_ids' => 'array',
            'village_ids' => 'array',
        ]);

        // Delete Regions
        if ($request->has('region_ids')) {
            Region::whereIn('id', $request->input('region_ids'))->delete();
        }

        // Delete Townships
        if ($request->has('township_ids')) {
            Township::whereIn('id', $request->input('township_ids'))->delete();
        }

        // Delete Villages
        if ($request->has('village_ids')) {
            Village::whereIn('id', $request->input('village_ids'))->delete();
        }

        // Delete Governorate and all related entities if no specific ids provided
        if (!$request->hasAny(['region_ids', 'township_ids', 'village_ids'])) {
            Governorate::where('id', $governorateId)->delete();
        }

        return response()->json([
            'message' => 'Governorate and related entities deleted successfully.',
        ], 200);
    }

    public function update(Request $request, $governorateId)
    {
        $request->validate([
            'name' => 'required|string|unique:governorates,name,' . $governorateId,
            'regions' => 'array',
            'townships' => 'array',
            'villages' => 'array',
        ]);

        // Update Governorate
        $governorate = Governorate::find($governorateId);

        if (!$governorate) {
            return response()->json([
                'message' => 'Governorate not found.',
            ], 404);
        }

        $governorate->update([
            'name' => $request->input('name'),
        ]);

        // Update Regions
        $governorate->regions()->delete(); // Delete existing regions
        foreach ($request->input('regions') as $regionName) {
            $region = Region::create([
                'name' => $regionName,
                'governorate_id' => $governorate->id,
            ]);
        }

        // Update Townships
        $governorate->townships()->delete(); // Delete existing townships
        foreach ($request->input('townships') as $townshipName) {
            $township = Township::create([
                'name' => $townshipName,
                'governorate_id' => $governorate->id,
            ]);
        }

        // Update Villages
        $governorate->villages()->delete(); // Delete existing villages
        foreach ($request->input('villages') as $villageName) {
            Village::create([
                'name' => $villageName,
                'governorate_id' => $governorate->id,
            ]);
        }

        return response()->json([
            'message' => 'Governorate and related entities updated successfully.',
            'data' => $governorate,
        ], 200);
    }

}
