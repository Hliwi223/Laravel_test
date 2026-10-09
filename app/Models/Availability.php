<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $table = 'availability';

    protected $fillable = [
        'product_id', 'pack_id', 'date',
        'quantity_total', 'quantity_reserved', 'quantity_available',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }
}
