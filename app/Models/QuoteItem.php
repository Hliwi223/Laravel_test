<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id', 'appliance_id', 'name', 'power_w', 'quantity',
        'duration_hours', 'usage_period', 'has_startup_surge',
        'startup_power_w', 'energy_wh',
    ];

    protected $casts = [
        'has_startup_surge' => 'boolean',
        'duration_hours' => 'float',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function appliance()
    {
        return $this->belongsTo(Appliance::class);
    }
}
