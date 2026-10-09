<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed', 'rejected'];

    protected $fillable = [
        'user_id', 'quote_id', 'pack_id',
        'customer_name', 'customer_phone', 'customer_email', 'cin',
        'project_type', 'start_date', 'end_date', 'location', 'address',
        'delivery_required', 'delivery_fee', 'status',
        'estimated_price', 'deposit_amount', 'customer_message',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'delivery_required' => 'boolean',
        'estimated_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'reservation_products')->withPivot('quantity')->withTimestamps();
    }

    /** Nombre de jours de location (minimum 1). */
    public function days(): int
    {
        return max(1, $this->start_date->diffInDays($this->end_date) + 1);
    }

    public function statusLabel(): string
    {
        return [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée',
            'rejected' => 'Refusée',
        ][$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'secondary',
            'completed' => 'primary',
            'rejected' => 'danger',
        ][$this->status] ?? 'secondary';
    }
}
