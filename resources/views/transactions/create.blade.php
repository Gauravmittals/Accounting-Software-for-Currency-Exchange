<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Lending Record</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
  <div class="max-w-xl mx-auto p-6 bg-white rounded-lg shadow mt-8">
    <div class="flex items-center mb-6">
      <a href="{{ route('lending.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">← Back</a>
      <h1 class="text-2xl font-bold">Add Lending Record</h1>
    </div>

    <form action="{{ route('lending.store') }}" method="POST" class="space-y-4">
      @csrf
      @include('lending._form', [
      'action' => route('lending.store'),
      'method' => 'POST',
      'model' => null
      ])



    </form>
  </div>
</body>

</html>