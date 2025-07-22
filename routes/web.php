<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');
Route::view('/transactions', 'transactions')->name('transactions.index');
Route::view('/inventory', 'inventory')->name('inventory.index');
Route::view('/lending', 'lending')->name('lending.index');
Route::view('/cash-in', 'cashin')->name('cash-in.index');
Route::view('/cashout', 'cashout')->name('cashout.index');


//cash-in controller

use App\Http\Controllers\CashInController;

Route::prefix('cash-in')->group(function () {
     Route::get('/', [CashInController::class, 'index'])->name('cash-in.index');
     Route::post('/currency-exchange', [CashInController::class, 'storeCurrencyExchange'])->name('cash-in.currency-exchange');
     Route::post('/cash-only', [CashInController::class, 'storeCashOnly'])->name('cash-in.cash-only');
     Route::delete('/{id}', [CashInController::class, 'destroy'])->name('cashin.destroy');
});
Route::put('/cashin/{id}', [CashInController::class, 'update'])->name('cashin.update');





//cash-out contorller
use App\Http\Controllers\CashOutController;

Route::get('/cash-out', [CashOutController::class, 'index'])
     ->name('cash-out.index');

Route::post('/cash-out/currency-exchange', [CashOutController::class, 'storeCurrencyExchange'])
     ->name('cash-out.currency-exchange');


Route::post('/cash-out/cash-only', [CashOutController::class, 'storeCashOnly'])
     ->name('cash-out.cash-only');

Route::delete('/cashout/{id}', [CashOutController::class, 'destroy'])->name('cashout.destroy');

Route::put('/cashout/{id}', [CashOutController::class, 'update'])->name('cashout.update');


//inventory

use App\Http\Controllers\InventoryController;

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/{currency}', [InventoryController::class, 'currencyLedger'])->name('inventory.currency');




//lending
use App\Http\Controllers\LendingController;

Route::prefix('lending')->group(function () {
     // List all cash-only lending entries
     Route::get('/', [LendingController::class, 'index'])->name('lending.index');
     // Show form to add a new cash-only record
     Route::get('/create', [LendingController::class, 'create'])->name('lending.create');
     // Store new record in CashIn or CashOut
     Route::post('/', [LendingController::class, 'store'])->name('lending.store');
     // Show edit form
     Route::get('/{type}/{id}/edit', [LendingController::class, 'edit'])
          ->whereIn('type', ['cashin', 'cashout'])
          ->name('lending.edit');
     // Update existing record
     Route::put('/{type}/{id}', [LendingController::class, 'update'])
          ->whereIn('type', ['cashin', 'cashout'])
          ->name('lending.update');
     // Delete record
     Route::delete('/{type}/{id}', [LendingController::class, 'destroy'])
          ->whereIn('type', ['cashin', 'cashout'])
          ->name('lending.destroy');
});
Route::get('/lending/{customer_name}', [LendingController::class, 'ledger'])->name('lending.ledger');



//transaction
use App\Http\Controllers\TransactionController;

Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('/today', [TransactionController::class, 'today'])->name('transactions.today');
Route::get('/cashin/create', [App\Http\Controllers\CashInController::class, 'create'])->name('cashin.create');
Route::get('/cashout/create', [App\Http\Controllers\CashOutController::class, 'create'])->name('cashout.create');
