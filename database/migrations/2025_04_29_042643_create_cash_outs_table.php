<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashOutsTable extends Migration
{
    public function up():void
    {
        Schema::create('cash_outs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['currency_exchange', 'cash_only']);
            $table->string('from_currency')->nullable();
            $table->string('to_currency')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->decimal('total', 15, 2);
            $table->string('customer_name')->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    public function down():void
    {
        Schema::dropIfExists('cash_outs');
    }
}
