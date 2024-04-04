<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DesireData;
use App\Models\SpecializationData;
use App\Models\JobDescriptionTable;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'birthDate',
        'cardNumber',
        'category',
        'certificate',
        'desiredGovernorate',
        'destination',
        'desireOrder',
        'exactSpecialization',
        'fullName',
        'governorate',
        'graduationDate',
        'graduationRate',
        'idNumber',
        'institute',
        'motherName',
        'residence',
        'notes',
        'named',
        // 'series',
        'exam_result',
        'the_ministry',
        'sub_entity',
        'gender',

        'lastModifier',
        'modificationDate',
        'recordEntry',
        'entryDate',
        'status',
        'accepted',
        'reason'
    ];


    public function desireData()
    {
        return $this->hasMany(DesireData::class);
    }

    public function specializationData()
    {
        return $this->hasMany(SpecializationData::class);
    }

    public function jobDescriptions()
    {
        return $this->belongsToMany(JobDescription::class, 'job_description_applicants')
            ->withTimestamps();
    }
    
}
