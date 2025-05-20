<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'car_number',
        'car_type_id',
        'car_class_id',
        'team_id',
    ];
}
