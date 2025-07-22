<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receive Money</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="container mx-auto mt-8">
        <div class="flex items-center mb-6">
            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                ← Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold">Purchase</h1>
        </div>

        <div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">

            <h2 class="text-xl font-bold mb-6 flex items-center">
                <span class="text-green-500 mr-2">✔Buy Currency </span>
            </h2>

            <div class="flex mb-6 border rounded overflow-hidden">
                <button id="currencyTab" class="flex-1 p-2 text-center bg-blue-100 font-medium" onclick="showCurrencyExchange()">💱 Currency Exchange</button>
                <button id="cashOnlyTab" class="flex-1 p-2 text-center bg-white font-medium" onclick="showCashOnly()">💵 Cash Only/Lent Money</button>
            </div>

            <!-- Currency Exchange Form -->
            <form id="currencyExchangeForm" action="{{ route('cash-in.currency-exchange') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-600">Required Information</h3>

                    <div>
                        <label class="block text-sm font-medium">Currency</label>
                        <select name="currency" required class="w-full mt-1 p-2 border rounded">
                            <option value="">Select currency</option>
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="AED">AED - UAE Dirham</option>
                            <option value="CAD">CAD - Canadian Dollar</option>
                            <option value="AUD">AUD - Australian Dollar</option>
                            <option value="SGD">SGD - Singapore Dollar</option>
                            <option value="NZD">NZD - NewZealand Dollar</option>
                            <option value="JPY">JPY - Japanese Yen</option>
                            <option value="CNY">CNY - Chinese Yuan</option>
                            <option value="RG">RG-Malaysian Ringgit</option>
                            <option value="TBH">TBH- Thailand Baht</option>
                            <option value="BEH">BEH - Bahraini Dinar</option>
                            <option value="SR">SR - Saudi Riyal</option>
                        </select>
                    </div>

                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium">Exchange Rate</label>
                            <input type="number" step="0.01" name="exchange_rate" id="exchangeRate" required class="w-full mt-1 p-2 border rounded">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium">Amount</label>
                            <input type="number" step="0.01" name="amount" id="amount" required class="w-full mt-1 p-2 border rounded">
                        </div>
                    </div>

                    <div class="bg-green-50 text-green-700 text-lg font-semibold px-4 py-3 rounded">
                        Total Amount: ₹<span id="totalAmount">0.00</span>
                    </div>

                    <h3 class="text-sm font-semibold text-gray-600 mt-6">Customer Information (Optional)</h3>

                    <input type="text" name="customer_name" placeholder="Enter customer name" class="w-full p-2 border rounded" />
                    <input type="text" name="mobile_number" placeholder="Enter mobile number" maxlength="15" class="w-full p-2 border rounded" />
                    <input type="text" name="reference" placeholder="Enter reference or purpose" class="w-full p-2 border rounded" />

                    <button type="submit" class="w-full mt-4 bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
                        ✓ Complete Transaction
                    </button>
                </div>
            </form>

            <!-- Cash Only Form -->
            <form id="cashOnlyForm" class="hidden" method="POST" action="{{ route('cash-in.cash-only') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Currency</label>
                        <select name="currency" id="currencySelect" class="w-full mt-1 p-2 border rounded">
                            <option value="">Select currency</option>
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="AED">AED - UAE Dirham</option>
                            <option value="CAD">CAD - Canadian Dollar</option>
                            <option value="AUD">AUD - Australian Dollar</option>
                            <option value="NZD">NZD - NewZealand Dollar</option>
                            <option value="SGD">SGD - Singapore Dollar</option>
                            <option value="JPY">JPY - Japanese Yen</option>
                            <option value="CNY">CNY - Chinese Yuan</option>
                            <option value="RG">RG-Malaysian Ringgit</option>
                            <option value="TBH">TBH- Thailand Baht</option>
                            <option value="BEH">BEH - Bahraini Dinar</option>
                            <option value="SR">SR - Saudi Riyal</option>

                        </select>
                    </div>

                    <div id="amountContainer" class="hidden">
                        <label class="block text-sm font-medium">Quantity</label>
                        <input type="number" step="0.01" name="amount" placeholder="Enter Quantity"
                            class="w-full p-2 border rounded" />

                    </div>
                    <label class="block text-sm font-medium">Total Amount</label>
                    <input type="number" step="0.01" name="total" placeholder="Enter Total Amount" required class="w-full p-2 border rounded" />
                    <label class="block text-sm font-medium">Customer Name</label>
                    <input type="text" name="customer_name" placeholder="Enter customer name" required class="w-full p-2 border rounded" />
                    <label class="block text-sm font-medium">Mobile Number</label>
                    <input type="text" name="mobile_number" placeholder="Enter mobile number" maxlength="15" class="w-full p-2 border rounded" />
                    <label class="block text-sm font-medium">Purpose</label>
                    <input type="text" name="purpose" placeholder="Enter purpose" maxlength="15" class="w-full p-2 border rounded" />

                    <button type="submit"
                        class="w-full mt-4 bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
                        ✓ Complete Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCurrencyExchange() {
            document.getElementById('currencyExchangeForm').classList.remove('hidden');
            document.getElementById('cashOnlyForm').classList.add('hidden');
            document.getElementById('currencyTab').classList.add('bg-blue-100');
            document.getElementById('cashOnlyTab').classList.remove('bg-blue-100');
        }

        function showCashOnly() {
            document.getElementById('cashOnlyForm').classList.remove('hidden');
            document.getElementById('currencyExchangeForm').classList.add('hidden');
            document.getElementById('cashOnlyTab').classList.add('bg-blue-100');
            document.getElementById('currencyTab').classList.remove('bg-blue-100');
        }
        const currencySelect = document.getElementById('currencySelect');
        const amountContainer = document.getElementById('amountContainer');

        currencySelect.addEventListener('change', function() {
            if (this.value) {
                amountContainer.classList.remove('hidden');
            } else {
                amountContainer.classList.add('hidden');
            }
        });


        const exchangeRateInput = document.getElementById('exchangeRate');
        const amountInput = document.getElementById('amount');
        const totalAmountSpan = document.getElementById('totalAmount');

        function updateTotal() {
            const rate = parseFloat(exchangeRateInput.value) || 0;
            const amount = parseFloat(amountInput.value) || 0;
            const total = rate * amount;
            totalAmountSpan.textContent = total.toFixed(2);
        }

        exchangeRateInput.addEventListener('input', updateTotal);
        amountInput.addEventListener('input', updateTotal);
    </script>
</body>

</html>