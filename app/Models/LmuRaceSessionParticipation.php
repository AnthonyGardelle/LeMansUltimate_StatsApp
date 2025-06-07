<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmuRaceSessionParticipation extends Model
{
    protected $fillable = [
        'lmu_session_participation_id',
        'grid_position',
        'class_grid_position',
        'finish_time',
    ];
}
