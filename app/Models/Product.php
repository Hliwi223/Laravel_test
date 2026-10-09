<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'reference', 'description',
        'power_w', 'voltage_v', 'capacity_wh', 'capacity_ah', 'surge_power_w',
        'daily_price', 'deposit', 'quantity', 'image', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'daily_price' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function packs()
    {
        return $this->belongsToMany(Pack::class, 'pack_products')->withPivot('quantity')->withTimestamps();
    }

    /**
     * Quantité réellement disponible sur une période donnée
     * (quantité totale moins ce qui est réservé par des réservations actives).
     */
    public function availableForPeriod($start, $end): int
    {
        $reserved = ReservationProduct::query()
            ->where('product_id', $this->id)
            ->whereHas('reservation', function ($q) use ($start, $end) {
                $q->whereIn('status', ['pending', 'confirmed'])
                  ->where('start_date', '<=', $end)
                  ->where('end_date', '>=', $start);
            })
            ->sum('quantity');

        return max(0, (int) $this->quantity - (int) $reserved);
    }
}
