<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transaction Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-8">
        <div class="flex items-center mb-6">
            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">← Back to Dashboard</a>
            <h1 class="text-2xl font-bold">Transaction Records</h1>
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
            </div>
        </form>

        <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">Trxn-ID</th>
                        <th class="px-4 py-2 border-b">Type-1</th>
                        <th class="px-4 py-2 border-b">Type-2</th>
                        <th class="px-4 py-2 border-b">Currency</th>
                        <th class="px-4 py-2 border-b">Amount</th>
                        <th class="px-4 py-2 border-b">Rate</th>
                        <th class="px-4 py-2 border-b">Total Amount</th>
                        <th class="px-4 py-2 border-b">Purpose/Reference</th>
                        <th class="px-4 py-2 border-b">Customer</th>
                        <th class="px-4 py-2 border-b">Date</th>
                        <th class="px-4 py-2 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $t)
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">
                            <span title="{{ $t->transaction_id }}">{{ substr($t->transaction_id, 0, 8) }}</span>
                        </td>
                        <td class="px-4 py-2 border-b">{{ $t->type ?? ($t instanceof \App\Models\CashIn ? 'Cash In' : 'Cash Out') }}</td>
                        <td class="px-4 py-2 border-b">{{ $t instanceof \App\Models\CashIn ? 'Cash In' : ($t instanceof \App\Models\CashOut ? 'Cash Out' : '-') }}</td>
                        <td class="px-4 py-2 border-b">{{ $t->currency }}</td>
                        <td class="px-4 py-2 border-b">{{ number_format($t->amount, 2) }}</td>
                        <td class="px-4 py-2 border-b">{{ $t->exchange_rate ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ number_format($t->total, 2) }}</td>
                        <td class="px-4 py-2 border-b">{{ $t->reference ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $t->customer_name ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y h:i A') }}</td>
                        <td class="px-4 py-2 border-b space-y-1">
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

                            <button
                                class="text-blue-600 hover:underline open-edit-btn"
                                data-record='@json($recordData)'>
                                Edit
                            </button>


                            @if($t instanceof \App\Models\CashIn)
                            <form action="{{ route('cashin.destroy', $t->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Cash In entry?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                            @elseif($t instanceof \App\Models\CashOut)
                            <form action="{{ route('cashout.destroy', $t->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Cash Out entry?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-4">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $records->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
        <div class="bg-white rounded-lg shadow p-6 w-full max-w-lg relative">
            <h2 class="text-xl font-semibold mb-4">Edit Transaction</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">

                <div class="mb-4">
                    <label class="block">Amount</label>
                    <input type="number" step="0.01" name="amount" id="editAmount" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block">Exchange Rate</label>
                    <input type="number" step="0.01" name="exchange_rate" id="editRate" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block">Total</label>
                    <input type="number" step="0.01" name="total" id="editTotal" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block">Reference / Purpose</label>
                    <input type="text" name="reference" id="editReference" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block">Customer Name</label>
                    <input type="text" name="customer_name" id="editCustomer" class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()" class="mr-3 text-gray-600">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Dropdown
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

        // Edit Modal Logic
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
</body>

</html>