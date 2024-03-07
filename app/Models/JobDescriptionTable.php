<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JobDescriptionSpecializationNeeded;
use App\Models\Applicant;

class JobDescriptionTable extends Model
{
    use HasFactory;
    protected $table = 'job_description_tables';

    protected $fillable = [
        'status',
        'category',
        'public_entity',
        'general',
        'precise',
        'sub_entity',
        'affiliate_entity',
        'sub_affiliate_entity',
        'gender_needed',
        'governorate',
        'job_title',
        'work_centers',
        'assignees',
        'vacancies',
        'card_number',
        'specialization',
        'record_entry',
        'entry_date',
        'last_modifier',
        'modification_date',
        'audited_by',
        'notes'
    ];

    public function applicants()
    {
        return $this->belongsToMany(Applicant::class, 'job_description_applicants')
            ->withTimestamps();
    }

    public function specializationNeeded()
    {
        return $this->hasMany(JobDescriptionSpecializationNeeded::class, 'job_description_id');
    }
    
}
