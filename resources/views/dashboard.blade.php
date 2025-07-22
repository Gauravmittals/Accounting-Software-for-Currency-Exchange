<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Exchange Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="min-h-screen bg-gray-100 flex flex-col">

        <!-- Fixed Header - white background -->
        <div class="fixed top-0 left-0 right-0 bg-white shadow-md z-10">
            <div class="container mx-auto p-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-blue-500">Money Exchange Dashboard</h1>

                <!-- Top Buttons -->
                <div class="flex space-x-4">
                    <a href="{{ route('transactions.index') }}" class="bg-white px-4 py-2 rounded shadow hover:bg-gray-100">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Transactions
                        </span>
                    </a>
                    <a href="{{ route('inventory.index') }}" class="bg-white px-4 py-2 rounded shadow hover:bg-gray-100">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Inventory
                        </span>
                    </a>
                    <a href="{{ route('lending.index') }}" class="bg-white px-4 py-2 rounded shadow hover:bg-gray-100">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Lending
                        </span>
                    </a>

                    <a href="{{ route('transactions.today') }}" class="bg-white px-4 py-2 rounded shadow hover:bg-gray-100">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Today
                        </span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-white px-4 py-2 rounded shadow hover:bg-gray-100">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Spacer to prevent content from being hidden behind fixed header -->
        <div class="h-24"></div>

        <!-- Dashboard Main Area - taking up most of the screen -->
        <div class="flex-1 container mx-auto py-4 px-4 flex flex-col">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 h-full">

                <!-- Cash In -->
                <a href="{{ route('cash-in.index') }}" class="bg-white flex flex-col items-center justify-center p-8 md:p-16 lg:p-24 xl:p-32 rounded-lg shadow hover:shadow-lg transition h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 md:h-32 md:w-32 lg:h-40 lg:w-40 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                    <span class="mt-8 text-green-500 font-bold text-3xl md:text-4xl">Currency In</span>
                </a>

                <!-- Cash Out -->
                <a href="{{ route('cash-out.index') }}" class="bg-white flex flex-col items-center justify-center p-8 md:p-16 lg:p-24 xl:p-32 rounded-lg shadow hover:shadow-lg transition h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 md:h-32 md:w-32 lg:h-40 lg:w-40 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span class="mt-8 text-red-500 font-bold text-3xl md:text-4xl">Currency Out</span>
                </a>
            </div>
        </div>
    </div>
</body>

</html>