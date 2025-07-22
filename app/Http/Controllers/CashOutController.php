<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashOut;
use App\Helpers\InventoryManager;

class CashOutController extends Controller
{
    /**
     * Show the Cash Out form.
     */
    public function index()
    {
        return view('cash-out/create');
    }

    /**
     * Store a currency exchange cash-out entry.
     */
    public function storeCurrencyExchange(Request $request)
    {
        $request->validate([
            'currency' => 'required|string',
            'exchange_rate' => 'required|numeric',
            'amount' => 'required|numeric',
            'customer_name' => 'nullable|string',
            'mobile_number' => 'nullable|string|max:15',
            'reference' => 'nullable|string',
        ]);

        // Update inventory first (check for availability)

        $transactionId = $this->generateTransactionId('CO', CashOut::class);

        // Record the cash-out transaction
        CashOut::create([
            'transaction_id' => $transactionId,
            'type' => 'currency_exchange',
            'currency' => $request->currency,
            'exchange_rate' => $request->exchange_rate,
            'amount' => $request->amount,
            'customer_name' => $request->customer_name,
            'mobile_number' => $request->mobile_number,
            'reference' => $request->reference,
            'total' => $request->amount * $request->exchange_rate,
        ]);

        $inventoryUpdated = InventoryManager::updateInventory(
            $request->currency,
            $request->amount,
            $request->exchange_rate,
            'out' // 'out' means currency sold
        );

        if (!$inventoryUpdated) {
            return back()->withErrors(['amount' => 'Not enough currency in inventory to complete this sale.']);
        }

        return redirect()->route('cash-out.index')->with('success', 'Currency exchange cash-out successful!');
    }

    /**
     * Store a cash-only withdrawal.
     */
    public function storeCashOnly(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'customer_name' => 'nullable|string',
            'mobile_number' => 'nullable|string|max:15',
            'purpose' => 'nullable|string',
        ]);

        $transactionId = $this->generateTransactionId('CO', CashOut::class);

        CashOut::create([
            'transaction_id' => $transactionId,
            'type' => 'cash_only',
            'currency' => $validated['currency'],
            'amount' => $validated['amount'],
            'customer_name' => $validated['customer_name'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'reference' => $validated['purpose'] ?? null,
            'total' => $validated['total'],
        ]);

        // ↑↑↑ Increase INR in inventory (Cash Out cash-only = Borrowed)
        if ($validated['currency'] === 'INR' && $validated['amount']) {
            InventoryManager::updateInventory(
                'INR',
                $validated['amount'],
                $validated['total'] / $validated['amount'],
                'in' // Borrowing = INR coming in
            );
        }

        return redirect()->back()->with('success', 'Cash given (borrowed) successfully!');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'nullable|numeric',
            'exchange_rate' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'customer_name' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $cashIn = CashOut::findOrFail($id);
        $cashIn->update([
            'amount' => $request->amount,
            'exchange_rate' => $request->exchange_rate,
            'total' => $request->total,
            'customer_name' => $request->customer_name,
            'reference' => $request->reference,
        ]);

        return redirect()->back()->with('success', 'Transaction updated successfully.');
    }


    // app/Http/Controllers/CashOutController.php
    public function destroy($id)
    {
        $record = \App\Models\CashOut::find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Cash Out record not found.');
        }

        $record->delete();
        return redirect()->back()->with('success', 'Cash Out record deleted.');
    }
    private function generateTransactionId($prefix, $modelClass)
    {
        $lastEntry = $modelClass::where('transaction_id', 'like', "$prefix-%")
            ->orderBy('_id', 'desc') // MongoDB's _id is timestamp-based
            ->first();

        $lastNumber = 0;
        if ($lastEntry && isset($lastEntry->transaction_id)) {
            $lastNumber = (int) str_replace("$prefix-", '', $lastEntry->transaction_id);
        }

        $newNumber = $lastNumber + 1;
        return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
