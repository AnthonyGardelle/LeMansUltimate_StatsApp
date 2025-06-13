<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmuLapSector extends Model
{
    protected $fillable = [
        'lmu_lap_id',
        'sector_number',
        'sector_time',
    ];
}
