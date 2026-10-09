<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
    protected $fillable = [
        'name', 'default_power_w', 'default_duration_hours',
        'has_startup_surge', 'startup_factor', 'category',
    ];

    protected $casts = [
        'has_startup_surge' => 'boolean',
        'default_power_w' => 'integer',
        'default_duration_hours' => 'float',
        'startup_factor' => 'float',
    ];
}
