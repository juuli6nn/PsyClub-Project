@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-4">
        @if ($selectedTransaction)
            <a href="{{ route('transactions.show', $selectedTransaction->id) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 text-xs hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">&larr; Back to Transaction</a>
        @else
            <a href="{{ route('payments.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 text-xs hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">&larr; Back to Payments</a>
        @endif
    </div>

    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6">
    <h1 class="text-xl font-medium text-white mb-4">Record Payment</h1>

    @if ($errors->any())
        <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($selectedTransaction)
        <div class="mb-5 p-3.5 border border-zinc-800 rounded bg-zinc-900/50 space-y-1.5 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-zinc-400">Transaction Reference</span>
                <span class="font-mono text-white font-medium">{{ $selectedTransaction->transaction_number }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-400">Member</span>
                <span class="text-zinc-200">{{ $selectedTransaction->member ? $selectedTransaction->member->first_name . ' ' . $selectedTransaction->member->last_name : 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-400">Level & Type</span>
                <span class="text-zinc-200 capitalize">{{ $selectedTransaction->level ? $selectedTransaction->level->name : 'N/A' }} ({{ $selectedTransaction->type }})</span>
            </div>
            <div class="flex items-center justify-between pt-1.5 border-t border-zinc-800">
                <span class="text-zinc-400">Remaining Balance Due</span>
                <span class="font-mono text-emerald-400 font-medium">${{ number_format($remainingBalance ?? 0, 2) }}</span>
            </div>
        </div>
    @endif

    <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
        @csrf

        @if ($selectedTransaction)
            <input type="hidden" name="membership_transaction_id" value="{{ $selectedTransaction->id }}">
        @else
            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Select Transaction:</label>
                <select name="membership_transaction_id" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                    <option value="">-- Choose a Pending Transaction --</option>
                    @foreach ($transactions as $txn)
                        @php
                            $paid = (float) $txn->payments->sum('amount');
                            $balance = max(0, (float) $txn->amount - $paid);
                        @endphp
                        @if ($balance > 0)
                            <option value="{{ $txn->id }}" {{ old('membership_transaction_id') == $txn->id ? 'selected' : '' }}>
                                {{ $txn->transaction_number }} — {{ $txn->member ? $txn->member->first_name . ' ' . $txn->member->last_name : 'N/A' }} (Balance: ${{ number_format($balance, 2) }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Payment Amount ($):</label>
            <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $remainingBalance !== null ? number_format($remainingBalance, 2, '.', '') : '') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Payment Method:</label>
            <select name="payment_method" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                @foreach (['Cash', 'GCash', 'Bank Transfer', 'Credit Card'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', 'Cash') === $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Payment Date:</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Notes (Optional):</label>
            <textarea name="notes" rows="2" placeholder="Receipt details or payment remarks..." class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">{{ old('notes') }}</textarea>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit" class="bg-white text-black text-xs font-medium px-4 py-2 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                Save Payment
            </button>
        </div>
    </form>

    </div>
</div>
@endsection
