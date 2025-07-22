<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class CashIn extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cash_ins'; // or your actual MongoDB collection name

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
