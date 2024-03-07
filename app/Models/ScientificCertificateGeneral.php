<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScientificCertificatePrecise;

class ScientificCertificateGeneral extends Model
{
    use HasFactory;
    protected $table = 'scientific_certificate_general';

    protected $fillable = ['name','category','type'];

    public static function boot()
    {
        parent::boot();

        
        static::deleting(function ($scientificCertificate) {
            $scientificCertificate->scientificCert()->delete();
        });
    }

    public function scientificCert()
    {
        return $this->hasMany(ScientificCertificatePrecise::class, 'certificate_general_id');
    }
    
    

}
