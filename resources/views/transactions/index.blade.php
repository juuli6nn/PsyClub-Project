@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-medium text-white">Transactions</h1>
    <a href="{{ route('transactions.create') }}" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors">
        New Transaction
    </a>
</div>

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <form action="{{ route('transactions.index') }}" method="GET" class="flex items-center gap-1.5">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="text" name="search" placeholder="Search ref # or member..." value="{{ request('search') }}" class="bg-zinc-900 border border-zinc-800 text-white text-xs rounded px-3 py-1.5 focus:outline-none focus:border-zinc-600 w-64 placeholder-zinc-500">
        <button type="submit" class="bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs font-medium px-3.5 py-1.5 rounded hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors cursor-pointer">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('transactions.index', request()->only('status')) }}" class="bg-zinc-900/60 border border-zinc-800 text-zinc-400 text-xs font-medium px-3 py-1.5 rounded hover:text-white hover:bg-zinc-800 transition-colors">
                Clear
            </a>
        @endif
    </form>

    <div class="flex flex-wrap items-center gap-1.5 text-xs">
        <a href="{{ route('transactions.index', request()->only('search')) }}" class="px-2.5 py-1 rounded border transition-colors {{ !request('status') ? 'bg-white text-black border-white font-medium' : 'border-zinc-800 bg-zinc-900 text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
            All
        </a>
        @foreach (['Pending', 'Paid', 'Approved', 'Expired', 'Cancelled'] as $statusOption)
            <a href="{{ route('transactions.index', array_merge(request()->only('search'), ['status' => $statusOption])) }}" class="px-2.5 py-1 rounded border transition-colors {{ request('status') === $statusOption ? 'bg-white text-black border-white font-medium' : 'border-zinc-800 bg-zinc-900 text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                {{ $statusOption }}
            </a>
        @endforeach
    </div>
</div>

@if (session('success'))
    <div class="mb-4 text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-900/60 rounded px-3 py-2">
        {{ session('success') }}
    </div>
@endif

<div class="border border-zinc-800 rounded-lg bg-zinc-950 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-800 bg-zinc-900/40 text-xs text-zinc-400">
                <tr>
                    <th class="px-4 py-2.5 font-medium">Reference #</th>
                    <th class="px-4 py-2.5 font-medium">Member</th>
                    <th class="px-4 py-2.5 font-medium">Type</th>
                    <th class="px-4 py-2.5 font-medium">Level</th>
                    <th class="px-4 py-2.5 font-medium">Amount</th>
                    <th class="px-4 py-2.5 font-medium">Status</th>
                    <th class="px-4 py-2.5 font-medium">Date</th>
                    <th class="px-4 py-2.5 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                @forelse ($transactions as $transaction)
                <tr class="hover:bg-zinc-900/30">
                    <td class="px-4 py-2.5 font-mono text-xs text-zinc-200">
                        <a href="{{ route('transactions.show', $transaction->id) }}" class="hover:underline underline-offset-2">
                            {{ $transaction->transaction_number }}
                        </a>
                    </td>
                    <td class="px-4 py-2.5 text-white font-medium">
                        @if ($transaction->member)
                            <a href="{{ route('members.show', $transaction->member->id) }}" class="hover:underline underline-offset-2">
                                {{ $transaction->member->first_name }} {{ $transaction->member->last_name }}
                            </a>
                        @else
                            <span class="text-zinc-500">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 capitalize text-xs text-zinc-300">{{ $transaction->type }}</td>
                    <td class="px-4 py-2.5 text-xs">{{ $transaction->level ? $transaction->level->name : 'N/A' }}</td>
                    <td class="px-4 py-2.5 font-mono text-xs text-zinc-200">${{ number_format($transaction->amount, 2) }}</td>
                    <td class="px-4 py-2.5 text-xs font-medium">
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
                    <td class="px-4 py-2.5 text-xs text-zinc-400">{{ $transaction->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-xs">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('transactions.show', $transaction->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                View
                            </a>
                            <a href="{{ route('transactions.receipt', $transaction->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                Receipt
                            </a>
                            @if ($transaction->status === 'Pending')
                                <a href="{{ route('payments.create', ['transaction_id' => $transaction->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-emerald-900/60 bg-emerald-950/30 text-emerald-400 hover:bg-emerald-900/40 hover:text-emerald-300 transition-colors">
                                    Pay
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-xs text-zinc-500">
                        No transactions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
