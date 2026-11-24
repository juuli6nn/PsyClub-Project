<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email — Membership System PsyClub</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Geist Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-zinc-200 font-sans min-h-screen antialiased flex items-center justify-center p-4 selection:bg-zinc-800 selection:text-white">
    <div class="w-full max-w-sm border border-zinc-800 rounded-lg bg-zinc-950 p-6 sm:p-8">
        <div class="mb-6 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Psyduck logo" class="mx-auto w-14 h-14 object-contain">
        </div>

        <h1 class="text-xl font-medium text-white mb-2 text-center">Verify Your Email</h1>
        <p class="text-xs text-zinc-400 leading-relaxed mb-6 text-center">
            We sent a verification link to <span class="text-white font-mono">{{ auth()->user()->email ?? 'your email' }}</span>. Please click the link inside that email to activate your admin access.
        </p>

        @if (session('success'))
            <div class="mb-4 text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-900/60 rounded p-3">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST" class="space-y-3">
            @csrf
            <button type="submit" class="w-full bg-white text-black text-xs font-medium px-4 py-2.5 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                Resend Verification Email
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-zinc-800/80 flex items-center justify-between text-xs text-zinc-400">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                Continue to Dashboard
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-400 hover:border-red-900/60 hover:bg-red-950/40 hover:text-red-400 transition-colors cursor-pointer">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</body>
</html>
