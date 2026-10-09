<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    protected $table = 'packs';

    protected $fillable = [
        'name', 'description', 'recommended_uses',
        'price_per_day', 'deposit', 'energy_capacity_wh',
        'continuous_power_w', 'surge_power_w', 'solar_power_w',
        'quantity', 'image', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_day' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'pack_products')->withPivot('quantity')->withTimestamps();
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'recommended_pack_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Nombre d'exemplaires du pack encore libres sur une période.
     */
    public function availableForPeriod($start, $end): int
    {
        $reserved = Reservation::query()
            ->where('pack_id', $this->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->count();

        return max(0, (int) $this->quantity - $reserved);
    }
}
