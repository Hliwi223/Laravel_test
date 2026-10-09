<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationProduct extends Model
{
    protected $fillable = ['reservation_id', 'product_id', 'quantity'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
