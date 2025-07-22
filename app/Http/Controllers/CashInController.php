<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashIn;
use App\Helpers\InventoryManager;

class CashInController extends Controller
{
    /**
     * Show the Cash In form.
     */
    public function index()
    {
        return view('cash-in');
    }

    /**
     * Store a currency exchange entry.
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

        $transactionId = $this->generateTransactionId('CI', CashIn::class);

        CashIn::create([
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

        // Update inventory using average method
        InventoryManager::updateInventory(
            $request->currency,
            $request->amount,
            $request->exchange_rate,
            'in' // 'in' means it's a purchase/received
        );

        return redirect()->back()->with('success', 'Currency exchange cash-in successful!');
    }

    /**
     * Store a cash only entry.
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

        $transactionId = $this->generateTransactionId('CI', CashIn::class);

        CashIn::create([
            'transaction_id' => $transactionId,
            'type' => 'cash_only',
            'currency' => $validated['currency'],
            'amount' => $validated['amount'],
            'customer_name' => $validated['customer_name'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'reference' => $validated['purpose'] ?? null,
            'total' => $validated['total'],
        ]);

        // ↓↓↓ Reduce INR from inventory (Cash In cash-only = Lent)
        if ($validated['currency'] === 'INR' && $validated['amount']) {
            InventoryManager::updateInventory(
                'INR',
                $validated['amount'],
                $validated['total'] / $validated['amount'],
                'out' // Lending = INR going out
            );
        }

        return redirect()->back()->with('success', 'Cash received (lent) successfully!');
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

        $cashIn = CashIn::findOrFail($id);
        $cashIn->update([
            'amount' => $request->amount,
            'exchange_rate' => $request->exchange_rate,
            'total' => $request->total,
            'customer_name' => $request->customer_name,
            'reference' => $request->reference,
        ]);

        return redirect()->back()->with('success', 'Transaction updated successfully.');
    }

    /**
     * Delete a Cash In entry.
     */
    public function destroy($id)
    {
        $record = CashIn::find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Cash In record not found.');
        }

        $record->delete();
        return redirect()->back()->with('success', 'Cash In record deleted.');
    }

    /**
     * Generate a custom transaction ID for Cash In.
     */
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
