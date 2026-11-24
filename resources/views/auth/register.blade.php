<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Admin Account — Membership System PsyClub</title>
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

        <h1 class="text-xl font-medium text-white mb-1 text-center">Sign up</h1>
        <p class="text-xs text-zinc-400 mb-6 text-center">Verify your email, then complete your administrator profile.</p>

        @if ($errors->any())
            <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.attempt') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="e.g. Julian725 (no spaces)" pattern="^\S+$" title="Username cannot contain spaces" required autofocus class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@gmail.com" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                <p class="text-[11px] text-zinc-500 mt-1">Use at least 8 characters.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" placeholder="••••••••" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
            </div>

            <div class="pt-1">
                <button type="submit" class="w-full bg-white text-black text-xs font-medium px-4 py-2.5 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                    Sign up
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-zinc-800/80 text-center text-xs text-zinc-400">
            Already have an account?
            <a href="{{ route('login') }}" class="text-white font-medium hover:underline underline-offset-4 ml-1">
                Sign in
            </a>
        </div>
    </div>
</body>
</html>
