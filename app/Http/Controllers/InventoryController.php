<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\CashIn;
use App\Models\CashOut;

class InventoryController extends Controller
{
    /**
     * Display the inventory list with all currencies.
     */
    public function index()
    {
        // Get all unique currencies from CashIn and CashOut
        $currencies = CashIn::pluck('currency')
            ->merge(CashOut::pluck('currency'))
            ->map(function ($c) {
                return $c ?: 'INR'; // Treat null as INR
            })
            ->unique();


        $inventories = collect();

        foreach ($currencies as $currency) {
            $cashIns = CashIn::where('currency', $currency)->get();
            $cashOuts = CashOut::where('currency', $currency)->get();

            $totalQuantityIn = $cashIns->sum('amount'); // Total currency purchased
            $totalQuantityOut = $cashOuts->sum('amount'); // Total currency sold
            $netQuantity = $totalQuantityIn - $totalQuantityOut;

            $totalAmountIn = $cashIns->sum('total'); // Total INR spent
            $totalAmountOut = $cashOuts->sum('total'); // Total INR received from sales
            $netAmount = $totalAmountIn - $totalAmountOut;

            if ($netQuantity <= 0) {
                continue; // skip if nothing is left in stock
            }

            $averageRate = $netQuantity > 0 ? $netAmount / $netQuantity : 0;

            $inventories->push((object)[
                'currency' => $currency,
                'total_quantity' => round($netQuantity, 2),
                'average_rate' => round($averageRate, 2),
                'average_price' => round($netAmount, 2),
            ]);
        }

        $totalInventoryValue = $inventories->sum('average_price');

        return view('inventory.index', compact('inventories', 'totalInventoryValue'));
    }

    /**
     * Show ledger for a specific currency, including both Cash In and Cash Out transactions.
     */
    public function currencyLedger($currency)
    {
        $cashInRecords = CashIn::where('currency', $currency)->get();
        $cashOutRecords = CashOut::where('currency', $currency)->get();

        $totalIn = $cashInRecords->sum('total');
        $totalOut = $cashOutRecords->sum('total');
        $netBalance = $totalIn - $totalOut;

        $records = $cashInRecords->merge($cashOutRecords)
            ->sortByDesc('created_at')
            ->values();

        return view('inventory.ledger', compact(
            'currency',
            'records',
            'totalIn',
            'totalOut',
            'netBalance'
        ));
    }
}
