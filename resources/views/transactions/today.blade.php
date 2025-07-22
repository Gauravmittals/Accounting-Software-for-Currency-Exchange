<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transaction Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">← Back to Dashboard</a>
            <h1 class="text-2xl font-bold">Today Transaction Records ({{ \Carbon\Carbon::today('Asia/Kolkata')->toFormattedDateString() }})</h1>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-green-100 p-6 rounded-lg shadow text-center">
                <h2 class="text-xl font-semibold text-green-600">Total Cash In</h2>
                <p class="text-3xl font-bold mt-2 text-green-800">₹{{ $totalCashIn }}</p>
            </div>
            <div class="bg-red-100 p-6 rounded-lg shadow text-center">
                <h2 class="text-xl font-semibold text-red-600">Total Cash Out</h2>
                <p class="text-3xl font-bold mt-2 text-red-800">₹{{ $totalCashOut }}</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="relative inline-block text-left">
                <button id="dropdownButton" type="button"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow focus:outline-none">
                    + Add New Transaction
                </button>
                <div id="dropdownMenu"
                    class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-10">
                    <a href="{{ route('cash-in.index') }}"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Add Cash In</a>
                    <a href="{{ route('cash-out.index') }}"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Add Cash Out</a>
                </div>
            </div>

            <form method="GET" class="bg-white p-4 rounded-lg shadow w-full sm:w-auto">
                <div class="flex flex-wrap gap-4 items-center">
                    <select name="type" class="p-2 border rounded w-full sm:w-auto">
                        <option value="">All Types</option>
                        <option value="cash_in" {{ request('type') == 'cash_in' ? 'selected' : '' }}>Cash In</option>
                        <option value="cash_out" {{ request('type') == 'cash_out' ? 'selected' : '' }}>Cash Out</option>
                    </select>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-all">Filter</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse shadow-md">
                <thead class="bg-gray-100">
                    <tr class="text-left">
                        <th class="px-4 py-2 border-b">Trxn-Id</th>
                        <th class="px-4 py-2 border-b">Type-1</th>
                        <th class="px-4 py-2 border-b">Type-2</th>
                        <th class="px-4 py-2 border-b">Currency</th>
                        <th class="px-4 py-2 border-b">Amount</th>
                        <th class="px-4 py-2 border-b">Rate(₹)</th>
                        <th class="px-4 py-2 border-b">Total Amount(₹)</th>
                        <th class="px-4 py-2 border-b">Customer</th>
                        <th class="p-4 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    @php
                    $recordData = [
                    '_id' => $t->_id,
                    'amount' => $t->amount,
                    'exchange_rate' => $t->exchange_rate,
                    'total' => $t->total,
                    'reference' => $t->reference,
                    'customer_name' => $t->customer_name,
                    'type' => $t instanceof \App\Models\CashIn ? 'cash_only' : 'cash_out'
                    ];
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">
                            <span title="{{ $t->transaction_id }}">{{ substr($t->transaction_id, 0, 8) }}</span>
                        </td>
                        <td class="px-4 py-2 border-b">{{ $t->type ?? ($t instanceof \App\Models\CashIn ? 'Cash In' : 'Cash Out') }}</td>
                        <td class="px-4 py-2 border-b">
                            {{ $t instanceof \App\Models\CashIn ? 'Cash In' : ($t instanceof \App\Models\CashOut ? 'Cash Out' : '-') }}
                        </td>
                        <td class="px-4 py-2 border-b">{{ $t->currency }}</td>
                        <td class="px-4 py-2 border-b">{{ number_format($t->amount) }}</td>
                        <td class="px-4 py-2 border-b">{{ $t->exchange_rate ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">₹{{ number_format($t->total) }}</td>
                        <td class="px-4 py-2 border-b">{{ $t->customer_name ?? '-' }}</td>
                        <td class="p-4 flex space-x-2">
                            <button class="text-blue-600 hover:underline open-edit-btn" data-record='@json($recordData)'>Edit</button>
                            <form action="{{ $t instanceof \App\Models\CashIn ? route('cashin.destroy', $t->_id) : route('cashout.destroy', $t->_id) }}"
                                method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-4 text-center text-gray-500">No transactions for today.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ✅ Edit Modal -->
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
        document.getElementById('dropdownButton').addEventListener('click', function() {
            const menu = document.getElementById('dropdownMenu');
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', function(event) {
            const button = document.getElementById('dropdownButton');
            const menu = document.getElementById('dropdownMenu');
            if (!button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        document.querySelectorAll('.open-edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const record = JSON.parse(this.dataset.record);
                openEditModal(record);
            });
        });

        function openEditModal(record) {
            const form = document.getElementById('editForm');

            form.action = record.type === 'cash_only' ?
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
    </script>
</body>

</html>