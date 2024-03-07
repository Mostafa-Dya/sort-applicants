<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Region;
use App\Models\Village;

class Township extends Model
{
    use HasFactory;

    protected $fillable = ['name','governorate_id'];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

}
