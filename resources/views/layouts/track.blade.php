<!DOCTYPE html>
<html lang="uk" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Перевірка статусу замовлення</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-base-200 min-h-screen flex flex-col">

    {{-- Шапка --}}
    <header class="bg-base-100 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
            <span class="text-xl font-bold text-primary">⚙ CRM Сервіс</span>
            <a href="/login" class="btn btn-ghost btn-sm">Увійти</a>
        </div>
    </header>

    {{-- Контент --}}
    <main class="flex-1 flex items-start justify-center p-4 pt-12">
        <div class="w-full max-w-2xl">
            @yield('content')
        </div>
    </main>

    {{-- Футер --}}
    <footer class="bg-base-100 border-t border-base-200 py-4 text-center text-sm text-base-content/50">
        {{ \App\Models\Setting::get('company_name') }} ·
        {{ \App\Models\Setting::get('company_phone') }}
    </footer>

    @livewireScripts
</body>
</html>