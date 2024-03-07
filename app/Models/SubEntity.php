<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PublicEntity;
use App\Models\AffiliatedEntity;

class SubEntity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'public_entity_id'];

    public static function boot()
    {
        parent::boot();



        static::deleting(function ($subEntity) {
            $subEntity->affiliatedEntities()->delete();
        });
    }
    public function publicEntity()
    {
        return $this->belongsTo(PublicEntity::class);
    }

    public function affiliatedEntities() // Update the method name to villages
    {
        return $this->hasMany(AffiliatedEntity::class);
    }
}
