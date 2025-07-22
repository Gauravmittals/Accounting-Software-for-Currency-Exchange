<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Inventory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'inventories';

    protected $fillable = [
        'currency',         // e.g. "USD - US Dollar"
        'total_quantity',   // e.g. 500.00
        'average_rate',     // e.g. 75.23  (₹ per unit)
        'average_price',    // total_quantity * average_rate
    ];

    // disable timestamps if you like, or leave on
    public $timestamps = true;
}
