<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Currency Inventory</title>
  <link
    href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
    rel="stylesheet" />
</head>

<body class="bg-gray-50">

  <div class="max-w-6xl mx-auto px-4 py-8">
    {{-- Back link + Title --}}
    <div class="flex items-center mb-6">
      <a href="{{ url('/dashboard') }}"
        class="flex items-center text-gray-600 hover:text-gray-900">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
          viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Back to Dashboard
      </a>
      <h1 class="ml-6 text-2xl font-bold">Currency Inventory</h1>
    </div>

    {{-- Summary Card --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <div class="flex items-start space-x-4">
        {{-- Box Icon --}}
        <svg class="h-6 w-6 text-blue-500 mt-1 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
          viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.732l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.732l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
          <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
          <line x1="12" y1="22.08" x2="12" y2="12"></line>
        </svg>

        <div>
          <h2 class="text-xl font-semibold">Current Inventory Status</h2>
          <p class="text-gray-500 mt-1">
            Overview of all currencies in stock with their quantities and average cost
          </p>
        </div>
      </div>
      <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow mb-6 text-lg font-semibold">
        Total Inventory Value: ₹{{ number_format($totalInventoryValue, 2) }}
      </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Currency
            </th>
            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Quantity
            </th>
            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Average Cost
            </th>
            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total Value
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($inventories as $inv)
          <tr class="hover:bg-gray-100">
            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
              <a href="{{ route('inventory.currency', ['currency' => urlencode($inv->currency)]) }}"
                class="text-blue-600 hover:underline">
                {{ $inv->currency }}
              </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-800">
              {{ number_format($inv->total_quantity, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-800">
              ₹{{ number_format($inv->average_rate, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-800">
              ₹{{ number_format($inv->average_price, 2) }}
            </td>
          </tr>

          @empty
          <tr>
            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
              No inventory data available.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</body>

</html>