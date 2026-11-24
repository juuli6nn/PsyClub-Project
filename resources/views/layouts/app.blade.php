<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Membership System — PsyClub' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Geist Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-zinc-200 font-sans min-h-screen antialiased selection:bg-zinc-800 selection:text-white print:bg-white print:text-black">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left Sidebar (Vercel Dark Style) -->
        <aside class="w-full md:w-60 border-b md:border-b-0 md:border-r border-zinc-800 bg-zinc-950 flex flex-col justify-between shrink-0 print:hidden">
            <div>
                <!-- Brand Header -->
                <div class="h-14 px-5 border-b border-zinc-800 flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                    <a href="{{ route('dashboard') }}" class="text-white text-sm font-medium tracking-tight hover:text-zinc-300 transition-colors">
                        PsyClub
                    </a>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="p-3 space-y-1 text-xs">
                    <div class="px-2.5 py-1.5 text-[10px] font-mono uppercase tracking-wider text-zinc-500">Overview</div>
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('/') || request()->is('dashboard') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z"/></svg>
                        <span>Dashboard</span>
                    </a>

                    <div class="pt-3 px-2.5 py-1.5 text-[10px] font-mono uppercase tracking-wider text-zinc-500">Directory</div>
                    <a href="{{ route('members.index') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('members*') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Members</span>
                    </a>
                    <a href="{{ route('membership-status.index') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('membership-status*') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Membership status</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('categories*') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10m-10 5h10"/></svg>
                        <span>Categories</span>
                    </a>
                    <a href="{{ route('levels.index') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('levels*') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span>Levels</span>
                    </a>

                    <div class="pt-3 px-2.5 py-1.5 text-[10px] font-mono uppercase tracking-wider text-zinc-500">Transactions</div>
                    <a href="{{ route('membership.create') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('membership/apply*') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Application Portal</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('transactions*') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Transactions</span>
                    </a>
                    <a href="{{ route('payments.index') }}" class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors {{ request()->is('payments*') ? 'bg-zinc-900 text-white font-medium border border-zinc-800' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Payments</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer (Bottom-Left Profile Button — Section IX) -->
            <div class="p-3.5 border-t border-zinc-800 flex items-center justify-between text-xs">
                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2.5 group">
                    <div class="w-7 h-7 rounded-full bg-amber-500 text-black font-semibold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="leading-tight">
                        <div class="text-white font-medium group-hover:underline underline-offset-2">
                            {{ auth()->user()->username ?? auth()->user()->name ?? 'admin' }}
                        </div>
                        <div class="text-[11px] text-zinc-500">Administrator</div>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-zinc-400 hover:text-white underline-offset-4 hover:underline cursor-pointer">
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Right Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-black print:bg-white">
            <!-- Top Header Bar -->
            <header class="h-14 border-b border-zinc-800 bg-black px-6 flex items-center justify-between print:hidden">
                <div class="flex items-center space-x-2 text-xs text-zinc-400">
                    <span>Workspace</span>
                    <span class="text-zinc-700">/</span>
                    <span class="text-white font-medium capitalize">
                        @if(request()->is('/') || request()->is('dashboard'))
                            Dashboard
                        @else
                            {{ explode('/', request()->path())[0] }}
                        @endif
                    </span>
                </div>

                <div class="flex items-center space-x-2">
                    @if(auth()->check() && ! auth()->user()->hasVerifiedEmail())
                        <a href="{{ route('verification.notice') }}" class="text-[11px] font-mono text-amber-400 border border-amber-900/60 bg-amber-950/40 px-2 py-0.5 rounded hover:bg-amber-900/50 transition-colors">
                            Verify Email
                        </a>
                    @endif
                </div>
            </header>

            <!-- Page Workspace -->
            <main class="flex-1 p-6 sm:p-8 max-w-6xl w-full mx-auto">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
