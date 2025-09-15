<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CODAPEC</title>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gradient-to-b from-indigo-50 via-white to-indigo-100 dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900 
             text-gray-900 dark:text-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <header class="w-full px-8 py-6 flex justify-between items-center">
        <h1 class="text-2xl font-extrabold text-indigo-700 dark:text-indigo-400 tracking-tight">
            CODAPEC ⚗️
        </h1>

        @if (Route::has('login'))
            <nav class="flex gap-4 items-center">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-600 rounded-lg hover:bg-indigo-50 dark:hover:bg-zinc-700 transition">
                        Log in
                    </a>
                @endauth
            </nav>
        @endif
    </header>

    <!-- Hero Section -->
    <main class="flex-1 flex flex-col items-center justify-center text-center px-6 relative overflow-hidden">
        <!-- Decorative background circle -->
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-indigo-200 dark:bg-indigo-800 rounded-full blur-3xl opacity-30"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-indigo-300 dark:bg-indigo-700 rounded-full blur-3xl opacity-20"></div>

        <h2 class="text-4xl lg:text-6xl font-extrabold tracking-tight mb-6 relative z-10">
            Managing <span class="text-indigo-600 dark:text-indigo-400">Chemical Distribution</span><br>
            with Transparency
        </h2>
        <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300 mb-10 max-w-2xl mx-auto relative z-10">
            CODAPEC helps track chemical requests, approvals, and dispatches across regions and districts.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
            <a href="{{ route('login') }}"
               class="px-8 py-3 bg-indigo-600 text-white rounded-lg shadow-lg hover:scale-105 transform transition">
               Get Started
            </a>
           
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white dark:bg-zinc-900 relative z-10">
        <div class="max-w-6xl mx-auto grid gap-10 sm:grid-cols-2 lg:grid-cols-3 px-6">
            <div class="p-8 bg-indigo-50 dark:bg-zinc-800 rounded-2xl shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold mb-3">📦 Dispatch Tracking</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Record multiple drivers, vehicles, and trip statuses for each chemical dispatch.
                </p>
            </div>

            <div class="p-8 bg-indigo-50 dark:bg-zinc-800 rounded-2xl shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold mb-3">✅ Multi-level Approvals</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Seamless approval workflow for DCOs, auditors, and regional managers.
                </p>
            </div>

            <div class="p-8 bg-indigo-50 dark:bg-zinc-800 rounded-2xl shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold mb-3">📊 Real-time Insights</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Instantly see requested, approved, dispatched, and remaining chemical quantities.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-6 text-center text-gray-500 text-sm border-t border-gray-200 dark:border-zinc-700">
        &copy; {{ date('Y') }} CODAPEC. All rights reserved.
    </footer>

</body>
</html>
