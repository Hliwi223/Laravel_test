<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'project_type', 'total_energy_wh', 'energy_with_margin_wh',
        'max_simultaneous_power_w', 'peak_power_w',
        'recommended_battery_wh', 'recommended_battery_ah', 'battery_voltage',
        'battery_chemistry', 'recommended_inverter_w', 'recommended_solar_w',
        'recommended_pack_id',
    ];

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function recommendedPack()
    {
        return $this->belongsTo(Pack::class, 'recommended_pack_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
