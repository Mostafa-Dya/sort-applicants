<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JobDescriptionTable;

class JobDescriptionSpecializationNeeded extends Model
{
    use HasFactory;
    protected $table = 'job_description_specialization_needed';

    protected $fillable = [
        'job_description_id',
        'degree',
        'specialization_needed',
        'specialization_needed_precise'
    ];

    // Add relationships if needed
    public function jobDescription()
    {
        return $this->belongsTo(JobDescriptionTable::class, 'job_description_id');
    }
}
