<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Region;
use App\Models\Township;
use App\Models\Village;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($governorate) {
            $governorate->regions()->delete();
            $governorate->townships()->delete();
            $governorate->villages()->delete();
        });
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
    public function townships() // Update the method name to townships
    {
        return $this->hasMany(Township::class);
    }

    public function villages() // Update the method name to villages
    {
        return $this->hasMany(Village::class);
    }
}
