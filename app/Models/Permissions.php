<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class Permissions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        'statusCheck',
        'sortable',
        
        'canActive',

        'addApplicants',
        'addCertificate',
        'addJobDescription',
        'addGovernorate',
        'addPublic',

        'editApplicants',
        'editCertificate',
        'editJobDescription',
        'editGovernorate',
        'editPublic',

        'deleteApplicants',
        'deleteCertificate',
        'deleteJobDescription',
        'deleteGovernorate',
        'deletePublic',

    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}

