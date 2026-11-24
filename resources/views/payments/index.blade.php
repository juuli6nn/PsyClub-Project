@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-medium text-white">Payments</h1>
    <a href="{{ route('payments.create') }}" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors">
        Record Payment
    </a>
</div>

<div class="mb-4">
    <form action="{{ route('payments.index') }}" method="GET" class="flex items-center gap-1.5">
        <input type="text" name="search" placeholder="Search payment ref, transaction, or member..." value="{{ request('search') }}" class="bg-zinc-900 border border-zinc-800 text-white text-xs rounded px-3 py-1.5 focus:outline-none focus:border-zinc-600 w-72 placeholder-zinc-500">
        <button type="submit" class="bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs font-medium px-3.5 py-1.5 rounded hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors cursor-pointer">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('payments.index') }}" class="bg-zinc-900/60 border border-zinc-800 text-zinc-400 text-xs font-medium px-3 py-1.5 rounded hover:text-white hover:bg-zinc-800 transition-colors">
                Clear
            </a>
        @endif
    </form>
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
                    <th class="px-4 py-2.5 font-medium">Payment Ref #</th>
                    <th class="px-4 py-2.5 font-medium">Transaction Ref #</th>
                    <th class="px-4 py-2.5 font-medium">Member</th>
                    <th class="px-4 py-2.5 font-medium">Method</th>
                    <th class="px-4 py-2.5 font-medium">Amount</th>
                    <th class="px-4 py-2.5 font-medium">Payment Date</th>
                    <th class="px-4 py-2.5 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                @forelse ($payments as $payment)
                <tr class="hover:bg-zinc-900/30">
                    <td class="px-4 py-2.5 font-mono text-xs text-zinc-200">{{ $payment->payment_reference }}</td>
                    <td class="px-4 py-2.5 font-mono text-xs text-zinc-400">
                        @if ($payment->transaction)
                            <a href="{{ route('transactions.show', $payment->transaction->id) }}" class="hover:text-white hover:underline underline-offset-2">
                                {{ $payment->transaction->transaction_number }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-white font-medium">
                        @if ($payment->transaction && $payment->transaction->member)
                            <a href="{{ route('members.show', $payment->transaction->member->id) }}" class="hover:underline underline-offset-2">
                                {{ $payment->transaction->member->first_name }} {{ $payment->transaction->member->last_name }}
                            </a>
                        @else
                            <span class="text-zinc-500">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-xs text-zinc-300">{{ $payment->payment_method }}</td>
                    <td class="px-4 py-2.5 font-mono text-xs text-emerald-400 font-medium">${{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-2.5 text-xs text-zinc-400">{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-xs">
                        @if ($payment->transaction)
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('transactions.show', $payment->transaction->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                    View
                                </a>
                                <a href="{{ route('transactions.receipt', $payment->transaction->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                    Receipt
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-xs text-zinc-500">
                        No payments recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
