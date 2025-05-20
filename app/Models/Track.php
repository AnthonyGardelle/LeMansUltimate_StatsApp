<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'track_venue',
        'track_course',
        'track_event',
        'track_length',
    ];
}
