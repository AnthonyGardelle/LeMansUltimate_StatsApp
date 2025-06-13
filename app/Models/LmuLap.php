<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmuLap extends Model
{
    protected $fillable = [
        'lmu_session_participation_id',
        'lmu_compound_id',
        'lap_number',
        'finish_position',
        'lap_time',
        'top_speed',
        'remaining_fuel',
        'fuel_used',
        'remaining_virtual_energy',
        'virtual_energy_used',
        'tire_wear_fl',
        'tire_wear_fr',
        'tire_wear_rl',
        'tire_wear_rr'
    ];
}
