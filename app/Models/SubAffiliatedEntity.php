<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAffiliatedEntity extends Model
{

    use HasFactory;

    protected $fillable = ['name', 'affiliated_entity_id'];


    public function affiliatedEntities() 
    {
        return $this->belongsTo(AffiliatedEntity::class);
    }
}
