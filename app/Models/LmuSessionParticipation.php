<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmuSessionParticipation extends Model
{
    protected $table = 'lmu_session_participations';

    protected $fillable = [
        'lmu_session_id',
        'driver_id',
        'car_id',
        'finish_position',
        'class_finish_position',
        'laps_completed',
        'pit_stops_executed',
        'best_lap_time',
        'finish_status',
        'dnf_reason',
    ];
}
