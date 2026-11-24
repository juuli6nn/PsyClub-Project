@extends('layouts.app')

@section('content')
@php
    $totalPaid = (float) $transaction->payments->sum('amount');
    $remainingBalance = max(0, (float) $transaction->amount - $totalPaid);
@endphp

<div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-4 print:hidden">
        <a href="{{ route('transactions.show', $transaction->id) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
            &larr; Back to Transaction
        </a>
        <button type="button" onclick="window.print()" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
            Print Receipt
        </button>
    </div>

    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6 sm:p-8 print:bg-white print:text-black print:border-zinc-300">
        <div class="flex items-start justify-between pb-6 mb-6 border-b border-zinc-800 print:border-zinc-300">
            <div>
                <h1 class="text-lg font-medium text-white print:text-black">Official Membership Receipt</h1>
                <p class="text-xs text-zinc-400 print:text-zinc-600 mt-0.5">Membership Management & Transaction Processing System</p>
            </div>
            <div class="text-right">
                <div class="font-mono text-sm font-medium text-white print:text-black">{{ $transaction->transaction_number }}</div>
                <div class="text-xs text-zinc-400 print:text-zinc-600 mt-0.5">{{ $transaction->created_at->format('F d, Y') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 pb-6 mb-6 border-b border-zinc-800 print:border-zinc-300 text-xs">
            <div>
                <div class="text-zinc-400 print:text-zinc-600 mb-1">Billed To</div>
                @if ($transaction->member)
                    <div class="text-white print:text-black font-medium text-sm">{{ $transaction->member->first_name }} {{ $transaction->member->last_name }}</div>
                    <div class="font-mono text-zinc-300 print:text-zinc-700 mt-0.5">{{ $transaction->member->membership_number }}</div>
                    <div class="text-zinc-400 print:text-zinc-600 mt-0.5">{{ $transaction->member->email }}</div>
                @else
                    <div class="text-zinc-500">N/A</div>
                @endif
            </div>
            <div class="text-right">
                <div class="text-zinc-400 print:text-zinc-600 mb-1">Transaction Status</div>
                <div class="font-medium text-sm {{ $transaction->status === 'Approved' ? 'text-emerald-400 print:text-black' : ($transaction->status === 'Paid' ? 'text-sky-400 print:text-black' : 'text-amber-400 print:text-black') }}">
                    {{ strtoupper($transaction->status) }}
                </div>
                <div class="text-zinc-400 print:text-zinc-600 mt-1">
                    Category: {{ $transaction->member && $transaction->member->category ? $transaction->member->category->name : 'N/A' }}
                </div>
            </div>
        </div>

        <div class="mb-6">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-zinc-800 print:border-zinc-300 text-zinc-400 print:text-zinc-600">
                    <tr>
                        <th class="pb-2 font-medium">Description</th>
                        <th class="pb-2 font-medium">Validity</th>
                        <th class="pb-2 font-medium text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-850 print:divide-zinc-200 text-zinc-200 print:text-black">
                    <tr>
                        <td class="py-3">
                            <div class="font-medium text-white print:text-black capitalize">{{ $transaction->type }} — {{ $transaction->level ? $transaction->level->name : 'Standard' }} Level</div>
                            @if ($transaction->notes)
                                <div class="text-zinc-400 print:text-zinc-600 mt-0.5">{{ $transaction->notes }}</div>
                            @endif
                        </td>
                        <td class="py-3 text-zinc-400 print:text-zinc-700">
                            {{ $transaction->level ? $transaction->level->validity_months . ' Months' : 'N/A' }}
                        </td>
                        <td class="py-3 text-right font-mono text-white print:text-black">
                            ${{ number_format($transaction->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6 pt-4 border-t border-zinc-800 print:border-zinc-300">
            <div class="text-xs font-medium text-zinc-300 print:text-black mb-2">Payment Details</div>
            @if ($transaction->payments->isEmpty())
                <p class="text-xs text-zinc-500">No payments recorded yet.</p>
            @else
                <div class="space-y-1.5 text-xs">
                    @foreach ($transaction->payments as $payment)
                        <div class="flex items-center justify-between text-zinc-300 print:text-zinc-800">
                            <span>
                                <span class="font-mono">{{ $payment->payment_reference }}</span>
                                &bull; {{ $payment->payment_method }}
                                &bull; {{ $payment->payment_date->format('M d, Y') }}
                            </span>
                            <span class="font-mono">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="pt-4 border-t border-zinc-800 print:border-zinc-300 space-y-1.5 text-xs">
            <div class="flex items-center justify-between text-zinc-400 print:text-zinc-600">
                <span>Total Fee</span>
                <span class="font-mono">${{ number_format($transaction->amount, 2) }}</span>
            </div>
            <div class="flex items-center justify-between text-zinc-400 print:text-zinc-600">
                <span>Total Paid</span>
                <span class="font-mono text-emerald-400 print:text-black">${{ number_format($totalPaid, 2) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm font-medium text-white print:text-black pt-2 border-t border-zinc-850 print:border-zinc-300">
                <span>Remaining Balance</span>
                <span class="font-mono">${{ number_format($remainingBalance, 2) }}</span>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-zinc-850 print:border-zinc-300 text-center text-[11px] text-zinc-500">
            Generated on {{ now()->format('F d, Y h:i A') }} &bull; Official System Receipt
        </div>
    </div>
</div>
@endsection
