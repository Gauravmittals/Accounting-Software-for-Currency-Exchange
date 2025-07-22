@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-xl shadow">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ url('/lending') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            ← Back to Dashboard
        </a>
        <h2 class="text-2xl font-bold">Ledger for: {{ $customer_name }}</h2>
        <button onclick="printLedger()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            🖨️ Print
        </button>
    </div>
    <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="p-2 border rounded w-full sm:w-auto">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="p-2 border rounded w-full sm:w-auto">
            <select name="type" class="p-2 border rounded w-full sm:w-auto">
                <option value="">All Types</option>
                <option value="cash_in" {{ request('type') == 'cash_in' ? 'selected' : '' }}>Cash In</option>
                <option value="cash_out" {{ request('type') == 'cash_out' ? 'selected' : '' }}>Cash Out</option>
            </select>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filter</button>
        </div>
    </form>

    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="bg-green-100 p-3 rounded text-green-800 font-semibold">
            Total Lent: ₹{{ number_format($totalLent, 2) }}
        </div>
        <div class="bg-red-100 p-3 rounded text-red-800 font-semibold">
            Total Borrowed: ₹{{ number_format($totalBorrowed, 2) }}
        </div>
        <div class="bg-gray-100 p-3 rounded text-gray-800 font-semibold">
            Net Balance: ₹{{ number_format($netBalance, 2) }}
        </div>
    </div>

    <div id="ledgerTable">
        <table class="min-w-full bg-white border border-gray-300">
            <thead class="bg-gray-200 text-left text-sm font-semibold text-gray-700">
                <tr>
                    <th class=" px-4 py-2 border-b">Trxn-Id</th>
                    <th class="px-4 py-2 border-b">Date</th>
                    <th class="px-4 py-2 border-b">Type</th>
                    <th class="px-4 py-2 border-b">Amount</th>
                    <th class="px-4 py-2 border-b">Reference</th>
                    <th class="px-4 py-2 border-b">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @foreach ($records as $record)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">
                        <span title="{{ $record->transaction_id }}">{{ substr($record->transaction_id, 0, 8) }}</span>
                    </td>
                    <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($record->created_at)->format('d M Y, h:i A') }}</td>
                    <td class="px-4 py-2 border-b capitalize">{{ $record->entry_type }}</td>
                    <td class="px-4 py-2 border-b">₹{{ number_format($record->total, 2) }}</td>
                    <td class="px-4 py-2 border-b">{{ $record->reference ?? '-' }}</td>
                    <td class="px-4 py-2 border-b space-y-1">
                        @php
                        $recordData = [
                        '_id' => $record->_id,
                        'amount' => $record->amount,
                        'exchange_rate' => $record->exchange_rate,
                        'total' => $record->total,
                        'reference' => $record->reference,
                        'customer_name' => $record->customer_name,
                        'type' => $record instanceof \App\Models\CashIn ? 'cash_only' : 'cash_out'
                        ];
                        @endphp

                        <button
                            class="text-blue-600 hover:underline open-edit-btn"
                            data-record='@json($recordData)'>
                            Edit
                        </button>


                        @if($record instanceof \App\Models\CashIn)
                        <form action="{{ route('cashin.destroy', $record->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Cash In entry?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                        @elseif($record instanceof \App\Models\CashOut)
                        <form action="{{ route('cashout.destroy', $record->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Cash Out entry?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Edit Transaction</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editId" name="_id">

            <div class="mb-4">
                <label class="block text-sm font-medium">Amount</label>
                <input type="number" name="amount" id="editAmount" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Exchange Rate (₹)</label>
                <input type="number" step="0.01" name="exchange_rate" id="editRate" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Total</label>
                <input type="number" name="total" id="editTotal" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Reference</label>
                <input type="text" name="reference" id="editReference" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Customer</label>
                <input type="text" name="customer_name" id="editCustomer" class="w-full p-2 border rounded">
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<script>
    function printLedger() {
        const printContents = document.getElementById("ledgerTable").innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
            <html>
            <head><title>Print Ledger</title></head>
            <body>${printContents}</body>
            </html>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // return to original page
    }


    function openEditModal(record) {
        const form = document.getElementById('editForm');
        const type = record.type || (record.currency && record.total ? 'cash_only' : 'cash_out');

        form.action = type === 'cash_in' || type === 'cash_only' || record.exchange_rate ?
            `/cashin/${record._id}` :
            `/cashout/${record._id}`;

        document.getElementById('editId').value = record._id;
        document.getElementById('editAmount').value = record.amount;
        document.getElementById('editRate').value = record.exchange_rate || '';
        document.getElementById('editTotal').value = record.total;
        document.getElementById('editReference').value = record.reference || '';
        document.getElementById('editCustomer').value = record.customer_name || '';

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Listen to all edit buttons and open modal with their data
    document.querySelectorAll('.open-edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const record = JSON.parse(this.dataset.record);
            openEditModal(record);
        });
    });
</script>
@endsection