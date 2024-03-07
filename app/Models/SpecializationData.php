<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Applicant;

class SpecializationData extends Model
{
    use HasFactory;
    protected $fillable = ['desire', 'namedVal', 'cardNumberVal','applicant_id'];
    protected $attributes = [
        'namedVal' => '', // Set a default value for publicEntity field
        'cardNumberVal'=> 0,
    ];
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
