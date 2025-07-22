<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lending - Customer List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="p-6">
    <h1 class="text-2xl font-bold mb-4">Lending Records</h1>

    <div class="bg-white shadow-md rounded p-4">
        <ul class="divide-y">
            @foreach($customers as $customer)
            <li class="py-2">
                <a href="{{ route('lending.ledger', $customer) }}" class="text-blue-600 hover:underline">
                    {{ $customer }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</body>

</html>