@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-xl shadow">
  <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">← Back to Dashboard</a>
  <h2 class="text-2xl font-bold mb-4 border-b pb-2">Lending Customers</h2>

  <input type="text" id="searchInput" placeholder="Search customers..."
    class="w-full mb-6 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

  <div id="customerList">
    @php
    $grouped = $customers->groupBy(fn($name) => strtoupper(substr($name, 0, 1)));
    @endphp

    @forelse($grouped as $letter => $names)
    <div class="mb-6">
      <h3 class="text-xl font-semibold text-gray-700 mb-2 border-b pb-1">{{ $letter }}</h3>
      <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($names as $name)
        <li class="customer-item">
          <a href="{{ route('lending.ledger', ['customer_name' => urlencode($name)]) }}"
            class="block p-4 bg-blue-100 hover:bg-blue-200 text-blue-900 rounded-lg shadow-sm transition duration-150">
            {{ $name }}
          </a>
        </li>
        @endforeach
      </ul>
    </div>
    @empty
    <p class="text-gray-500">No lending records found.</p>
    @endforelse
  </div>
</div>

<script>
  const input = document.getElementById('searchInput');
  const customerList = document.getElementById('customerList');

  input.addEventListener('input', function() {
    const filter = input.value.toLowerCase();
    const sections = customerList.querySelectorAll('div.mb-6');

    sections.forEach(section => {
      const items = section.querySelectorAll('.customer-item');
      let visibleCount = 0;

      items.forEach(item => {
        const name = item.textContent.toLowerCase();
        if (name.includes(filter)) {
          item.classList.remove('hidden');
          visibleCount++;
        } else {
          item.classList.add('hidden');
        }
      });

      // Hide the entire section if no names are visible
      if (visibleCount === 0) {
        section.classList.add('hidden');
      } else {
        section.classList.remove('hidden');
      }
    });
  });
</script>
@endsection