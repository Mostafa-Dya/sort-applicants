<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublicEntity;
use App\Models\SubEntity;
use App\Models\AffiliatedEntity;
use App\Models\SubAffiliatedEntity;

class PublicEntityController extends Controller
{            // print_r($rowData);

    public function create(Request $request)
    {
        try {
            $validationRules = [
                'name' => 'required|string|unique:public_entities,name',
                'sub_entities' => 'array',
            ];
    
            $request->validate($validationRules);
    
            // Log request data for debugging
            \Log::info('Request Data:', $request->all());
    
            // Create PublicEntity
            $publicEntity = PublicEntity::create([
                'name' => $request->input('name'),
            ]);
    
            // Create SubEntities
            foreach ($request->input('sub_entities') as $subEntityData) {
                $subEntity = SubEntity::create([
                    'name' => $subEntityData['name'],
                    'public_entity_id' => $publicEntity->id,
                ]);
    
                // Check if affiliated_entities is an array
                if (isset($subEntityData['affiliated_entities']) && is_array($subEntityData['affiliated_entities'])) {
                    foreach ($subEntityData['affiliated_entities'] as $affiliatedEntityData) {
                        // Create AffiliatedEntity
                        $affiliatedEntity = AffiliatedEntity::create([
                            'name' => $affiliatedEntityData['name'],
                            'sub_entity_id' => $subEntity->id,
                        ]);
    
                        // Check if sub_affiliated_entities is an array
                        if (isset($affiliatedEntityData['sub_affiliated_entities']) && is_array($affiliatedEntityData['sub_affiliated_entities'])) {
                            foreach ($affiliatedEntityData['sub_affiliated_entities'] as $subAffiliatedEntityData) {
                                // Create SubAffiliatedEntity
                                SubAffiliatedEntity::create([
                                    'name' => $subAffiliatedEntityData['name'],
                                    'affiliated_entity_id' => $affiliatedEntity->id,
                                ]);
                            }
                        }
                    }
                }
            }
    
            return response()->json([
                'message' => 'PublicEntity and related entities created successfully.',
                'data' => $publicEntity,
            ], 201);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error creating entities: ' . $e->getMessage());
    
            // Return a meaningful error response
            return response()->json(['error' => 'Failed to create entities. ' . $e->getMessage()], 500);
        }
    }
    
    
    
    
    
    
    public function getAll()
    {
        $publicEntities = PublicEntity::with('subEntities.affiliatedEntities.subAffiliatedEntities')->get();
    
        $formattedData = $publicEntities->map(function ($publicEntity) {
            return [
                'id' => $publicEntity->id,
                'name' => $publicEntity->name,
                'sub_entities' => $publicEntity->subEntities->map(function ($subEntity) {
                    return [
                        'id' => $subEntity->id,
                        'public_entity_id' => $subEntity->public_entity_id,
                        'name' => $subEntity->name,
                        'affiliated_entities' => $subEntity->affiliatedEntities->map(function ($affiliatedEntity) {
                            return [
                                'id' => $affiliatedEntity->id,
                                'sub_entity_id' => $affiliatedEntity->sub_entity_id,
                                'name' => $affiliatedEntity->name,
                                'sub_affiliated_entities' => $affiliatedEntity->subAffiliatedEntities->toArray(),
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ];
        });
    
        return response()->json([
            'data' => $formattedData,
        ], 200);
    }
    
    public function getById($publicEntityId)
    {
        $publicEntity = PublicEntity::with('subEntities.affiliatedEntities.subAffiliatedEntities')->find($publicEntityId);
    
        if (!$publicEntity) {
            return response()->json([
                'message' => 'PublicEntity not found.',
            ], 404);
        }
    
        $formattedData = [
            'id' => $publicEntity->id,
            'name' => $publicEntity->name,
            'sub_entities' => $publicEntity->subEntities->map(function ($subEntity) {
                return [
                    'id' => $subEntity->id,
                    'public_entity_id' => $subEntity->public_entity_id,
                    'name' => $subEntity->name,
                    'affiliated_entities' => $subEntity->affiliatedEntities->map(function ($affiliatedEntity) {
                        return [
                            'id' => $affiliatedEntity->id,
                            'sub_entity_id' => $affiliatedEntity->sub_entity_id,
                            'name' => $affiliatedEntity->name,
                            'sub_affiliated_entities' => $affiliatedEntity->subAffiliatedEntities->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    
        return response()->json([
            'data' => $formattedData,
        ], 200);
    }
    
    

    public function deletePublicEntity(Request $request, $publicEntityId)
    {
        $subEntityIds = $request->input('sub_entities', []); 
    
        $deletedSubEntityIds = [];
    
        // If you want to delete SubEntities based on IDs provided
        if (!empty($subEntityIds)) {
            SubEntity::whereIn('id', $subEntityIds)->delete();
            $deletedSubEntityIds = $subEntityIds;
        }
    
        // Delete PublicEntity and its remaining SubEntities if no specific SubEntities provided
        if (empty($subEntityIds)) {
            PublicEntity::where('id', $publicEntityId)->delete();
        }
    
        return response()->json([
            'message' => 'PublicEntity and related entities deleted successfully.',
            'deleted_sub_entities' => $deletedSubEntityIds,
        ], 200);
    }
    

    public function deleteAffiliatedEntities(Request $request)
    {
        $affiliatedEntityIds = $request->input('affiliated_entity_ids', []);
    
        $deletedAffiliatedEntityIds = [];
    
        // Delete AffiliatedEntities
        if (!empty($affiliatedEntityIds)) {
            AffiliatedEntity::whereIn('id', $affiliatedEntityIds)->delete();
            $deletedAffiliatedEntityIds = $affiliatedEntityIds;
        }
    
        return response()->json([
            'message' => 'AffiliatedEntities deleted successfully.',
            'deleted_affiliated_entities' => $deletedAffiliatedEntityIds,
        ], 200);
    }

    public function deleteSubAffiliatedEntities(Request $request)
    {
        $subAffiliatedEntityIds = $request->input('sub_affiliated_entity_ids', []);

        $deletedSubAffiliatedEntityIds = [];

        // Delete SubAffiliatedEntities
        if (!empty($subAffiliatedEntityIds)) {
            SubAffiliatedEntity::whereIn('id', $subAffiliatedEntityIds)->delete();
            $deletedSubAffiliatedEntityIds = $subAffiliatedEntityIds;
        }

        return response()->json([
            'message' => 'Sub Affiliated Entities deleted successfully.',
            'deleted_sub_affiliated_entities' => $deletedSubAffiliatedEntityIds,
        ], 200);
    }
    

    

public function update(Request $request, $publicEntityId)
{
    $request->validate([
        'name' => 'required|string|unique:public_entities,name,' . $publicEntityId,
        'sub_entities' => 'array',
    ]);

    try {
        // Update PublicEntity
        $publicEntity = PublicEntity::find($publicEntityId);

        if (!$publicEntity) {
            return response()->json([
                'message' => 'PublicEntity not found.',
            ], 404);
        }

        $publicEntity->update([
            'name' => $request->input('name'),
        ]);

        // Delete existing sub-entities and affiliated entities
        $publicEntity->subEntities()->delete();
        SubAffiliatedEntity::whereIn('sub_affiliated_entity_id', $publicEntity->subEntities()->pluck('id'))->delete();

        // Create new sub-entities and affiliated entities
        foreach ($request->input('sub_entities') as $subEntityData) {
            $subEntity = SubEntity::create([
                'name' => $subEntityData['name'],
                'public_entity_id' => $publicEntity->id,
            ]);

            if (isset($subEntityData['affiliated_entities']) && is_array($subEntityData['affiliated_entities'])) {
                foreach ($subEntityData['affiliated_entities'] as $affiliatedEntity) {
                    AffiliatedEntity::create([
                        'name' => $affiliatedEntity['name'],
                        'sub_entity_id' => $subEntity->id,
                    ]);
                }
            }

            if (isset($subEntityData['sub_affiliated_entities']) && is_array($subEntityData['sub_affiliated_entities'])) {
                foreach ($subEntityData['sub_affiliated_entities'] as $subAffiliatedEntityName) {
                    SubAffiliatedEntity::create([
                        'name' => $subAffiliatedEntityName,
                        'sub_affiliated_entity_id' => $subEntity->id,
                    ]);
                }
            }
        }

        $publicEntity->refresh(); // Refresh the model to get the latest data after updates

        return response()->json([
            'message' => 'PublicEntity, SubEntities, and AffiliatedEntities updated successfully.',
            'data' => $publicEntity,
        ], 200);
    } catch (\Exception $e) {
        // Log the error or return a meaningful error response
        return response()->json(['error' => 'Failed to update entities. ' . $e->getMessage()], 500);
    }
}

    
    
    
    
    
    
}
