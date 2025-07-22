<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'customers';

    protected $fillable = [
        'name',        // Customer's name (should be unique)
        'contact',     // Optional: phone or email
        'address',     // Optional: for records
    ];

    public $timestamps = true;
}
