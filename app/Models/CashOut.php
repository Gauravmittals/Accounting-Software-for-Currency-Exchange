<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CashOut extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cash_outs'; // or your actual MongoDB collection name

    protected $fillable = [
        'transaction_id',
        'type',
        'currency',
        'exchange_rate',
        'amount',
        'total',
        'customer_name',
        'mobile_number',
        'reference',
        
    ];
}
