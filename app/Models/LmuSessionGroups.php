<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmuSessionGroups extends Model
{
    protected $fillable = [
        'starting_at',
        'hashcode',
    ];
}
