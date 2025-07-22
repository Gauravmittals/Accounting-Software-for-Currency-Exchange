<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashInsTable extends Migration
{
    public function up(): void
    {
        Schema::create('cash_ins', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['currency_exchange', 'cash_only']);
            $table->string('from_currency')->nullable(); // for currency_exchange
            $table->string('to_currency')->nullable();   // for currency_exchange
            $table->decimal('exchange_rate', 10, 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->nullable(); // for cash_only
            $table->decimal('total', 12, 2);
            $table->string('customer_name')->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_ins');
    }
}
