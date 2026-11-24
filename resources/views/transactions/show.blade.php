@extends('layouts.app')

@section('content')
@php
    $totalPaid = (float) $transaction->payments->sum('amount');
    $remainingBalance = max(0, (float) $transaction->amount - $totalPaid);
@endphp

<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 text-xs hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
            &larr; Back to Transactions
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,2fr)_minmax(20rem,1fr)] gap-6 items-start">
    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6">
    @if (session('success'))
        <div class="mb-4 text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-900/60 rounded px-3 py-2">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-800/80">
        <div>
            <h1 class="text-xl font-medium text-white font-mono">{{ $transaction->transaction_number }}</h1>
            <p class="text-xs text-zinc-400 mt-0.5 capitalize">{{ $transaction->type }} Transaction</p>
        </div>
        <div class="flex items-center space-x-2">
            @if ($remainingBalance > 0 && !in_array($transaction->status, ['Cancelled', 'Expired'], true))
                <a href="{{ route('payments.create', ['transaction_id' => $transaction->id]) }}" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors">
                    Record Payment
                </a>
            @endif

            @if ($transaction->status === 'Approved')
                <span class="text-xs font-medium text-emerald-400 border border-emerald-900/60 bg-emerald-950/40 px-2.5 py-1 rounded">Approved</span>
            @elseif ($transaction->status === 'Paid')
                <span class="text-xs font-medium text-sky-400 border border-sky-900/60 bg-sky-950/40 px-2.5 py-1 rounded">Paid</span>
            @elseif ($transaction->status === 'Pending')
                <span class="text-xs font-medium text-amber-400 border border-amber-900/60 bg-amber-950/40 px-2.5 py-1 rounded">Pending</span>
            @elseif ($transaction->status === 'Expired')
                <span class="text-xs font-medium text-zinc-400 border border-zinc-800 bg-zinc-900 px-2.5 py-1 rounded">Expired</span>
            @else
                <span class="text-xs font-medium text-red-400 border border-red-900/60 bg-red-950/40 px-2.5 py-1 rounded">{{ $transaction->status }}</span>
            @endif
        </div>
    </div>

    <div class="space-y-3 text-sm">
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Reference Number</span>
            <span class="font-mono text-xs text-zinc-200">{{ $transaction->transaction_number }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Member</span>
            @if ($transaction->member)
                <a href="{{ route('members.show', $transaction->member->id) }}" class="text-white font-medium hover:underline underline-offset-2">
                    {{ $transaction->member->first_name }} {{ $transaction->member->last_name }}
                </a>
            @else
                <span class="text-zinc-500">N/A</span>
            @endif
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Membership Number</span>
            <span class="font-mono text-xs text-zinc-300">{{ $transaction->member ? $transaction->member->membership_number : 'N/A' }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Transaction Type</span>
            <span class="text-zinc-200 capitalize">{{ $transaction->type }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Membership Level</span>
            <span class="text-zinc-200">{{ $transaction->level ? $transaction->level->name : 'N/A' }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Total Amount</span>
            <span class="font-mono text-white font-medium">${{ number_format($transaction->amount, 2) }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Amount Paid</span>
            <span class="font-mono text-emerald-400">${{ number_format($totalPaid, 2) }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Remaining Balance</span>
            <span class="font-mono {{ $remainingBalance > 0 ? 'text-amber-400' : 'text-zinc-400' }}">${{ number_format($remainingBalance, 2) }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
            <span class="text-xs text-zinc-400">Date Created</span>
            <span class="text-zinc-300">{{ $transaction->created_at->format('F d, Y h:i A') }}</span>
        </div>
        @if ($transaction->notes)
        <div class="py-1.5 border-b border-zinc-900">
            <span class="block text-xs text-zinc-400 mb-1">Notes</span>
            <p class="text-xs text-zinc-300">{{ $transaction->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Recorded Payments Section -->
    <div class="mt-6 pt-4 border-t border-zinc-800/80">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-medium text-zinc-300">Recorded Payments ({{ $transaction->payments->count() }})</h2>
            @if ($remainingBalance > 0 && !in_array($transaction->status, ['Cancelled', 'Expired'], true))
                <a href="{{ route('payments.create', ['transaction_id' => $transaction->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                    + Add Payment
                </a>
            @endif
        </div>

        @if ($transaction->payments->isEmpty())
            <p class="text-xs text-zinc-500 py-2">No payments recorded for this transaction yet.</p>
        @else
            <div class="border border-zinc-800/80 rounded bg-zinc-900/30 divide-y divide-zinc-800/60">
                @foreach ($transaction->payments as $payment)
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-mono text-zinc-200 font-medium">{{ $payment->payment_reference }}</div>
                            <div class="text-zinc-400 mt-0.5">
                                {{ $payment->payment_method }} &bull; {{ $payment->payment_date->format('M d, Y') }}
                                @if ($payment->notes)
                                    &bull; <span class="text-zinc-500">{{ $payment->notes }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="font-mono text-emerald-400 font-medium">
                            +${{ number_format($payment->amount, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($transaction->status === 'Paid')
        <div class="mt-4 flex justify-end">
            <form action="{{ route('transactions.update-status', $transaction->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="Approved">
                <button type="submit" class="bg-emerald-500 text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-emerald-400 transition-colors cursor-pointer">
                    Approve
                </button>
            </form>
        </div>
    @endif

    <div class="mt-6 pt-4 border-t border-zinc-850 flex flex-wrap items-center justify-end gap-2 text-xs">
        <a href="{{ route('transactions.receipt', $transaction->id) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
            Print Receipt
        </a>
        @if ($transaction->member)
            <a href="{{ route('members.show', $transaction->member->id) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                View Member Profile
            </a>
        @endif
    </div>
    </div>

    @if ($transaction->status !== 'Paid')
        <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6">
            <h2 class="text-base font-medium text-white mb-4">Update Status</h2>
            <form action="{{ route('transactions.update-status', $transaction->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="status" class="block text-xs font-medium text-zinc-300 mb-1">Transaction status</label>
                    <select id="status" name="status" class="w-full bg-zinc-900 border border-zinc-800 rounded px-2.5 py-1.5 text-xs text-zinc-100 focus:outline-none focus:border-zinc-500">
                        @foreach (['Pending', 'Paid', 'Approved', 'Expired', 'Cancelled'] as $statusOption)
                            <option value="{{ $statusOption }}" {{ $transaction->status === $statusOption ? 'selected' : '' }}>
                                {{ $statusOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="text-[11px] text-zinc-500">Only paid transactions can be marked as Approved.</p>
                <div class="flex justify-end">
                    <button type="submit" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                        Save Status
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
</div>
@endsection
