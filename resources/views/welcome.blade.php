@extends('layouts.app')

@section('content')
@php
    $activeRate = ($totalMembers ?? 0) > 0 ? round((($activeMembers ?? 0) / $totalMembers) * 100) : 0;
@endphp

<div class="space-y-6">
    @if (session('success'))
        <div class="text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-900/60 rounded px-3.5 py-2.5">
            {{ session('success') }}
        </div>
    @endif

    <!-- Section III Greeting & Live Weather Header -->
    <div class="flex flex-wrap items-end justify-between gap-4 pb-4 border-b border-zinc-800/80">
        <div>
            <h1 class="text-2xl font-medium text-white">Hello, {{ auth()->user()->username ?? auth()->user()->name ?? 'admin' }}!</h1>
            <div class="mt-1 text-xs text-zinc-400 space-y-0.5">
                <div id="dashboard-weather" class="text-emerald-400 font-medium">Loading live weather...</div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs font-medium px-3.5 py-2 rounded hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                Update address
            </a>
            <a href="{{ route('members.create') }}" class="bg-white text-black text-xs font-medium px-3.5 py-2 rounded hover:bg-zinc-200 transition-colors">
                Register member
            </a>
        </div>
    </div>

    <!-- Top Row: 4 KPI Cards (Matching Reference Orientation in Vercel Dark Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Collected Payments -->
        <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-xs text-zinc-400">
                    <span>Collected Payments</span>
                    <span class="font-mono text-[10px] text-zinc-500">TOTAL</span>
                </div>
                <div class="text-2xl font-medium text-white font-mono mt-2">
                    ${{ number_format($collectedPayments ?? 0, 2) }}
                </div>
                <div class="mt-3 text-xs text-zinc-400 flex items-center space-x-2">
                    <span>Recorded receipts:</span>
                    <span class="text-emerald-400 font-mono">{{ $totalPaymentsCount ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-zinc-800/80 flex items-center justify-between text-xs text-zinc-400">
                <span>Approved txns</span>
                <span class="font-mono text-zinc-200">{{ $approvedTransactions ?? 0 }}</span>
            </div>
        </div>

        <!-- Card 2: Total Members -->
        <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-xs text-zinc-400">
                    <span>Total Members</span>
                    <span class="font-mono text-[10px] text-zinc-500">DIRECTORY</span>
                </div>
                <div class="text-2xl font-medium text-white font-mono mt-2">
                    {{ $totalMembers ?? 0 }}
                </div>
                <div class="mt-3 w-full bg-zinc-900 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-white h-full rounded-full" style="width: {{ min(100, max(10, ($totalMembers ?? 0) * 10)) }}%"></div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-zinc-800/80 flex items-center justify-between text-xs text-zinc-400">
                <span>Active members</span>
                <span class="font-mono text-emerald-400">{{ $activeMembers ?? 0 }}</span>
            </div>
        </div>

        <!-- Card 3: Pending Transactions -->
        <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-xs text-zinc-400">
                    <span>Pending Transactions</span>
                    <span class="font-mono text-[10px] text-zinc-500">QUEUE</span>
                </div>
                <div class="text-2xl font-medium text-amber-400 font-mono mt-2">
                    {{ $pendingTransactions ?? 0 }}
                </div>
                <div class="mt-3 text-xs text-zinc-400">
                    Awaiting payment settlement
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-zinc-800/80 flex items-center justify-between text-xs">
                <a href="{{ route('transactions.index', ['status' => 'Pending']) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                    Process queue &rarr;
                </a>
            </div>
        </div>

        <!-- Card 4: Active Rate / Operation Status -->
        <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-xs text-zinc-400">
                    <span>Active Member Rate</span>
                    <span class="font-mono text-[10px] text-zinc-500">STATUS</span>
                </div>
                <div class="text-2xl font-medium text-emerald-400 font-mono mt-2">
                    {{ $activeRate }}%
                </div>
                <div class="mt-3 w-full bg-zinc-900 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-400 h-full rounded-full" style="width: {{ $activeRate }}%"></div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-zinc-800/80 flex items-center justify-between text-xs text-zinc-400">
                <span>Inactive</span>
                <span class="font-mono text-zinc-300">{{ max(0, ($totalMembers ?? 0) - ($activeMembers ?? 0)) }}</span>
            </div>
        </div>
    </div>

    <!-- Bottom Main Panel: 2-Column Split (Transactions Left + Level Ranking Right) -->
    <div class="border border-zinc-800 rounded-lg bg-zinc-950 overflow-hidden">
        <!-- Panel Header Tabs & Quick Actions -->
        <div class="px-5 py-3.5 border-b border-zinc-800 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center space-x-4 text-xs">
                <span class="text-white font-medium border-b-2 border-white pb-3 -mb-3.5">Transactions</span>
                <a href="{{ route('members.index') }}" class="text-zinc-400 hover:text-white transition-colors">Members</a>
                <a href="{{ route('payments.index') }}" class="text-zinc-400 hover:text-white transition-colors">Payments</a>
            </div>

            <div class="flex items-center space-x-2 text-xs">
                <a href="{{ route('transactions.create') }}" class="bg-white text-black font-medium px-3 py-1 rounded hover:bg-zinc-200 transition-colors">
                    + New Transaction
                </a>
                <a href="{{ route('payments.create') }}" class="border border-zinc-700 text-zinc-200 font-medium px-3 py-1 rounded hover:bg-zinc-800 hover:text-white transition-colors">
                    Record Payment
                </a>
            </div>
        </div>

        <!-- Panel Body: Left Table (2/3) + Right Level Ranking (1/3) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-zinc-800">
            <!-- Left 2/3: Recent Transactions -->
            <div class="lg:col-span-2 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xs font-medium text-zinc-300 uppercase tracking-wider font-mono">Recent Activity</h2>
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-xs text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                        View all &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-zinc-800 text-xs text-zinc-400">
                            <tr>
                                <th class="pb-2.5 font-medium">Reference #</th>
                                <th class="pb-2.5 font-medium">Member</th>
                                <th class="pb-2.5 font-medium">Type</th>
                                <th class="pb-2.5 font-medium">Amount</th>
                                <th class="pb-2.5 font-medium">Status</th>
                                <th class="pb-2.5 font-medium text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                            @forelse ($recentTransactions ?? [] as $transaction)
                                <tr class="hover:bg-zinc-900/30">
                                    <td class="py-2.5 font-mono text-xs text-zinc-200">
                                        <a href="{{ route('transactions.show', $transaction->id) }}" class="hover:underline underline-offset-2">
                                            {{ $transaction->transaction_number }}
                                        </a>
                                    </td>
                                    <td class="py-2.5 text-xs text-white font-medium">
                                        @if ($transaction->member)
                                            <a href="{{ route('members.show', $transaction->member->id) }}" class="hover:underline underline-offset-2">
                                                {{ $transaction->member->first_name }} {{ $transaction->member->last_name }}
                                            </a>
                                        @else
                                            <span class="text-zinc-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-xs capitalize text-zinc-400">{{ $transaction->type }}</td>
                                    <td class="py-2.5 font-mono text-xs text-zinc-200">${{ number_format($transaction->amount, 2) }}</td>
                                    <td class="py-2.5 text-xs font-medium">
                                        @if ($transaction->status === 'Approved')
                                            <span class="text-emerald-400">Approved</span>
                                        @elseif ($transaction->status === 'Paid')
                                            <span class="text-sky-400">Paid</span>
                                        @elseif ($transaction->status === 'Pending')
                                            <span class="text-amber-400">Pending</span>
                                        @elseif ($transaction->status === 'Expired')
                                            <span class="text-zinc-500">Expired</span>
                                        @else
                                            <span class="text-red-400">{{ $transaction->status }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-right whitespace-nowrap text-xs">
                                        @if ($transaction->status === 'Pending')
                                            <a href="{{ route('payments.create', ['transaction_id' => $transaction->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-emerald-900/60 bg-emerald-950/30 text-emerald-400 hover:bg-emerald-900/40 hover:text-emerald-300 transition-colors">Pay</a>
                                        @else
                                            <a href="{{ route('transactions.show', $transaction->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">View</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-xs text-zinc-500">
                                        No transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right 1/3: Membership Level Ranking -->
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xs font-medium text-zinc-300 uppercase tracking-wider font-mono">Level Ranking</h2>
                    <a href="{{ route('levels.index') }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-xs text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                        Manage
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($levelsBreakdown ?? [] as $index => $level)
                        <div class="flex items-center justify-between py-1.5">
                            <div class="flex items-center space-x-3">
                                <span class="w-5 h-5 rounded-full text-[11px] font-mono flex items-center justify-center {{ $index < 2 ? 'bg-white text-black font-bold' : 'bg-zinc-900 text-zinc-400 border border-zinc-800' }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="text-xs font-medium text-white">{{ $level->name }}</div>
                                    <div class="text-[11px] text-zinc-500 font-mono">${{ number_format($level->fee, 2) }} / {{ $level->validity_months }}m</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-mono text-xs text-zinc-200">{{ $level->members_count }}</span>
                                <span class="text-[11px] text-zinc-500">members</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500">No levels configured.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const lat = {{ (float) (auth()->user()->latitude ?? 14.5995) }};
    const lon = {{ (float) (auth()->user()->longitude ?? 120.9842) }};
    const el = document.getElementById('dashboard-weather');
    if (el) {
        fetch(`/weather?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`)
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(w => { el.textContent = `${w.place}: ${w.temperature}°C, ${w.description}, humidity ${w.humidity}%`; })
            .catch(() => { el.textContent = 'Weather is temporarily unavailable.'; });
    }
});
</script>

@endsection
