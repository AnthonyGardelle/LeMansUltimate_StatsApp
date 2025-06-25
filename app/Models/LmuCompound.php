<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmuCompound extends Model
{

    protected $table = 'lmu_compounds';

    protected $fillable = [
        'front_compound',
        'rear_compound'
    ];
}
