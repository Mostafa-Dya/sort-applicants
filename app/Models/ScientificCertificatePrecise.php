<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScientificCertificateGeneral;

class ScientificCertificatePrecise extends Model
{
    use HasFactory;
    protected $table = 'scientific_certificate_precise';

    protected $fillable = ['name','category', 'certificate_general_id'];

    public function scientificGeneral()
    {
        return $this->belongsTo(ScientificCertificateGeneral::class, 'certificate_general_id');
    }
}
