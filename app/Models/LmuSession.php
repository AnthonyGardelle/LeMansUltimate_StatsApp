<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmuSession extends Model
{
    protected $fillable = [
        'lmu_session_type_id',
        'track_id',
        'lmu_session_group_id',
        'starting_at',
        'duration',
        'mech_fail_rate',
        'damage_multiplier',
        'fuel_multiplier',
        'tire_multiplier',
        'parc_ferme',
        'fixed_setups',
        'free_settings',
        'fixed_upgrades',
        'limited_tires',
        'tire_warmers'
    ];
}
