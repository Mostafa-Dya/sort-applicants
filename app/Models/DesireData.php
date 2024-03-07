<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Applicant;

class DesireData extends Model
{
    use HasFactory;
    protected $fillable = [
        'governorateDesire',
        'publicEntitySide',
        'cardNumberDesire',
        'publicEntity',
        'numberOfCenters',
        'jobTitle',
        'primarySpecialization',
        'applicant_id',
        'specifiedSpecialization'
    ];

    protected $attributes = [
        'publicEntity' => '', // Set a default value for publicEntity field
        'numberOfCenters'=> 0,
        'jobTitle' => '', // Set a default value for publicEntity field
        'primarySpecialization' => '', // Set a default value for publicEntity field
        'specifiedSpecialization' => '', // Set a default value for publicEntity field
    ];
    

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
