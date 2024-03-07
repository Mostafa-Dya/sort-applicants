<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PublicEntity;

class AffiliatedEntity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sub_entity_id'];



    public function subEntity()
    {
        return $this->belongsTo(SubEntity::class);
    }

    public function subAffiliatedEntities()
    {
        return $this->hasMany(SubAffiliatedEntity::class);
    }
}
