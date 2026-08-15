<!DOCTYPE html>
<html lang="uk" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CRM' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
            <script>
            // Зберігаємо стан sidebar в localStorage
            document.addEventListener('DOMContentLoaded', function() {
                const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
                if (collapsed) document.body.classList.add('sidebar-collapsed');
            });
            function toggleSidebar() {
                const collapsed = document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar_collapsed', collapsed);
            }
        </script>
        <style>
            .sidebar-collapsed aside { width: 64px !important; }
            .sidebar-collapsed aside .nav-label { display: none; }
            .sidebar-collapsed aside .logo-text { display: none; }
            .sidebar-collapsed aside .user-info { display: none; }
            .sidebar-collapsed aside .menu-title { display: none; }
            aside { transition: width 0.2s ease; overflow: hidden; }
        </style>
    @livewireStyles
</head>
<body class="bg-base-200 min-h-screen">

    {{-- Sidebar --}}
    <div class="drawer lg:drawer-open">
        <input id="drawer" type="checkbox" class="drawer-toggle">

        <div class="drawer-content flex flex-col">
            {{-- Topbar --}}
            <div class="navbar bg-base-100 shadow-sm lg:hidden">
                <label for="drawer" class="btn btn-ghost btn-square">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </label>
                <span class="text-lg font-bold">CRM</span>
            </div>

            {{-- Основний контент --}}
            <main class="flex-1 p-4 lg:p-6">
                @yield('content')
            </main>
        </div>

        {{-- Sidebar меню --}}
        <div class="drawer-side z-40">
            <label for="drawer" class="drawer-overlay"></label>
            <aside class="bg-base-100 w-64 min-h-full flex flex-col shadow-lg">

                {{-- Логотип --}}
                <div class="p-4 border-b border-base-200">
                    <span class="text-xl font-bold text-primary nav-label">⚙ CRM Сервіс</span>
                    <button onclick="toggleSidebar()" 
                            class="btn btn-ghost btn-square btn-sm flex-shrink-0"
                            title="Згорнути меню">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                {{-- Навігація --}}
                @auth
                <nav class="flex-1 p-2">
                    <ul class="menu menu-md gap-1">

                        <li class="menu-title">Головне</li>
                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                               <span class="nav-label"> Дашборд</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('orders.index') }}"
                               class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span class="nav-label">Заявки</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('clients.index') }}"
                               class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="nav-label">Клієнти</span>
                            </a>
                        </li>

                        <li class="menu-title mt-2">Склад</li>
                        <li>
                            <a href="{{ route('inventory.index') }}"
                               class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span class="nav-label">Запчастини</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('purchases.index') }}"
                               class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="nav-label">Закупівлі</span>
                            </a>
                        </li>

                        <li class="menu-title mt-2">Фінанси</li>
                        <li>
                            <a href="{{ route('finance.index') }}"
                               class="{{ request()->routeIs('finance.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="nav-label">Фінанси</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('salary.index') }}"
                               class="{{ request()->routeIs('salary.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="nav-label">Зарплата</span>
                            </a>
                        </li>

                        @can('settings.manage')
                        <li class="menu-title mt-2">Система</li>
                        <li>
                            <a href="{{ route('references.index') }}"
                            class="{{ request()->routeIs('references.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="nav-label">Довідники</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}"
                               class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                               <span class="nav-label"> Користувачі</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}"
                               class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="nav-label">Налаштування</span>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </nav>

                {{-- Профіль користувача --}}
                <div class="p-3 border-t border-base-200">
                    <div class="flex items-center gap-3">
                        <div class="avatar placeholder">
                            <div class="bg-primary text-primary-content rounded-full w-9">
                                <span class="text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-base-content/60 truncate">{{ auth()->user()->getRoleNames()->first() }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-square btn-sm" title="Вийти">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
@endauth
            </aside>
        </div>
    </div>

    @livewireScripts
</body>
</html>