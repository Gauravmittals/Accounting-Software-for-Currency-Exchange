<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashIn;
use App\Models\CashOut;

class LendingController extends Controller
{
    /**
     * Show unique customer names from cash-only lending records.
     */
    public function index()
    {
        $cashInCustomers = CashIn::where('type', 'cash_only')
            ->whereNotNull('customer_name')
            ->pluck('customer_name');

        $cashOutCustomers = CashOut::where('type', 'cash_only')
            ->whereNotNull('customer_name')
            ->pluck('customer_name');

        $customers = $cashInCustomers->merge($cashOutCustomers)
            ->filter(fn($name) => !empty(trim($name)))
            ->unique()
            ->sort()
            ->values();

        return view('lending.index', compact('customers'));
    }

    /**
     * Show full ledger for a selected customer from cash-only records.
     */
    public function ledger($customer_name)
    {
        $customer_name = urldecode($customer_name);
        $trimmedName = trim($customer_name);

        $cashInRecords = CashIn::where('type', 'cash_only')
            ->whereRaw([
                'customer_name' => [
                    '$regex' => '^' . preg_quote($trimmedName) . '$',
                    '$options' => 'i'
                ]
            ])
            ->get()
            ->map(function ($record) {
                $record->entry_type = 'lent';
                return $record;
            });

        $cashOutRecords = CashOut::where('type', 'cash_only')
            ->whereRaw([
                'customer_name' => [
                    '$regex' => '^' . preg_quote($trimmedName) . '$',
                    '$options' => 'i'
                ]
            ])
            ->get()
            ->map(function ($record) {
                $record->entry_type = 'borrowed';
                return $record;
            });

        $records = $cashInRecords->merge($cashOutRecords)->sortBy('created_at');

        $totalLent     = $cashInRecords->sum('total');
        $totalBorrowed = $cashOutRecords->sum('total');
        $netBalance    = $totalBorrowed - $totalLent;

        return view('lending.ledger', compact(
            'customer_name',
            'records',
            'totalLent',
            'totalBorrowed',
            'netBalance'
        ));
    }

    /**
     * Show form to add a new cash-only lending record.
     */
    public function create()
    {
        return view('lending.create');
    }

    /**
     * Store a new cash-only record into either CashIn or CashOut.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_type'    => 'required|in:cashin,cashout',
            'amount'        => 'nullable|numeric',
            'customer_name' => 'nullable|string',
            'mobile_number' => 'nullable|string|max:15',
            'reference'     => 'nullable|string',
            'total'         => 'required|numeric',
        ]);

        $isCashIn = $data['entry_type'] === 'cashin';
        $model = $isCashIn ? new CashIn() : new CashOut();
        $prefix = $isCashIn ? 'CI' : 'CO';
        $modelClass = $isCashIn ? CashIn::class : CashOut::class;

        $model->transaction_id = $this->generateTransactionId($prefix, $modelClass);
        $model->type           = 'cash_only';
        $model->currency       = 'INR';
        $model->amount         = $data['amount'];
        $model->total          = $data['total'];
        $model->customer_name  = $data['customer_name'];
        $model->mobile_number  = $data['mobile_number'];
        $model->reference      = $data['reference'];
        $model->save();

        return redirect()->route('lending.index')->with('success', 'Record added successfully.');
    }

    /**
     * Show the edit form for a given cash-only record.
     */
    public function edit($type, $id)
    {
        $model = $type === 'cashin'
            ? CashIn::findOrFail($id)
            : CashOut::findOrFail($id);

        return view('lending.edit', [
            'model'      => $model,
            'entry_type' => $type,
        ]);
    }

    /**
     * Update the specified cash-only record.
     */
    public function update(Request $request, $type, $id)
    {
        $data = $request->validate([
            'amount'        => 'nullable|numeric',
            'customer_name' => 'nullable|string',
            'mobile_number' => 'nullable|string|max:15',
            'reference'     => 'nullable|string',
            'total'         => 'required|numeric',
        ]);

        $model = $type === 'cashin'
            ? CashIn::findOrFail($id)
            : CashOut::findOrFail($id);

        $model->amount         = $data['amount'];
        $model->total          = $data['total'];
        $model->customer_name  = $data['customer_name'];
        $model->mobile_number  = $data['mobile_number'];
        $model->reference      = $data['reference'];
        $model->save();

        return redirect()->route('lending.index')->with('success', 'Record updated successfully.');
    }

    /**
     * Delete the specified cash-only record.
     */
    public function destroy($type, $id)
    {
        $model = $type === 'cashin'
            ? CashIn::findOrFail($id)
            : CashOut::findOrFail($id);

        $model->delete();

        return redirect()->route('lending.index')->with('success', 'Record deleted successfully.');
    }

    /**
     * Generate a custom transaction ID.
     */
    private function generateTransactionId($prefix, $modelClass)
    {
        $lastEntry = $modelClass::where('transaction_id', 'like', "$prefix-%")
            ->orderBy('_id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastEntry && isset($lastEntry->transaction_id)) {
            $lastNumber = (int) str_replace("$prefix-", '', $lastEntry->transaction_id);
        }

        $newNumber = $lastNumber + 1;
        return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
