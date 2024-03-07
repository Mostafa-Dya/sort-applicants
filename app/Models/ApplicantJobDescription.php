<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantJobDescription extends Model
{
    use HasFactory;

    protected $table = 'applicant_job_description'; 

    protected $fillable = [
        'applicant_id',
        'job_description_id',
    ];
}
