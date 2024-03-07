<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubEntity;
use App\Models\AffiliatedEntity;

class PublicEntity extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($publicEntity) {
            $publicEntity->subEntities()->delete();

        });
    }

    public function subEntities()
    {
        return $this->hasMany(SubEntity::class);
    }



}
