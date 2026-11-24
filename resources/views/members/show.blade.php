@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('members.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 text-xs hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
            &larr; Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6">
        @if (session('success'))
            <div class="mb-4 text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-900/60 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-800/80">
            <h1 class="text-xl font-medium text-white">{{ $member->first_name }} {{ $member->last_name }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('members.edit', $member->id) }}" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors">
                    Edit Member
                </a>
                <form action="{{ route('members.toggle-status', $member->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 text-xs hover:border-amber-900/60 hover:bg-amber-950/40 hover:text-amber-400 transition-colors cursor-pointer">
                        {{ $member->is_active ? 'Deactivate Member' : 'Activate Member' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
                <span class="text-xs text-zinc-400">Membership Number</span>
                <span class="font-mono text-xs text-zinc-200">{{ $member->membership_number }}</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
                <span class="text-xs text-zinc-400">Name</span>
                <span class="text-white font-medium">{{ $member->first_name }} {{ $member->last_name }}</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
                <span class="text-xs text-zinc-400">Email</span>
                <span class="text-zinc-300">{{ $member->email }}</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
                <span class="text-xs text-zinc-400">Phone Number</span>
                <span class="text-zinc-300">{{ $member->phone_number ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
                <span class="text-xs text-zinc-400">Category</span>
                <span class="text-zinc-200">{{ $member->category ? $member->category->name : 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
                <span class="text-xs text-zinc-400">Level</span>
                <span class="text-zinc-200">{{ $member->level ? $member->level->name : 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-zinc-900">
                <span class="text-xs text-zinc-400">Status</span>
                @if ($member->is_active)
                    <span class="text-xs text-emerald-400 font-medium">Active</span>
                @else
                    <span class="text-xs text-red-400 font-medium">Inactive</span>
                @endif
            </div>
            <div class="flex items-center justify-between py-1.5">
                <span class="text-xs text-zinc-400">Joined</span>
                <span class="text-zinc-300">{{ $member->created_at ? $member->created_at->format('F d, Y') : 'N/A' }}</span>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-zinc-850 flex flex-wrap items-center justify-end gap-2 text-xs">
            <a href="{{ route('members.print', $member->id) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                Print Record
            </a>
        </div>
    </div>

    <!-- Transaction & Payment History Box -->
    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-medium text-white">Transaction & Payment History</h2>
            <a href="{{ route('transactions.create', ['member_id' => $member->id]) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                + New Transaction
            </a>
        </div>

        @if ($member->transactions->isEmpty())
            <p class="text-xs text-zinc-500 py-3">No transactions recorded for this member yet.</p>
        @else
            <div class="space-y-3">
                @foreach ($member->transactions as $transaction)
                    @php
                        $paidAmount = (float) $transaction->payments->sum('amount');
                        $balance = max(0, (float) $transaction->amount - $paidAmount);
                    @endphp
                    <div class="border border-zinc-800/80 rounded bg-zinc-900/30 p-3.5 text-xs space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('transactions.show', $transaction->id) }}" class="font-mono text-white font-medium hover:underline underline-offset-2">
                                    {{ $transaction->transaction_number }}
                                </a>
                                <span class="text-zinc-600">&bull;</span>
                                <span class="capitalize text-zinc-300">{{ $transaction->type }}</span>
                                <span class="text-zinc-600">&bull;</span>
                                <span class="text-zinc-400">{{ $transaction->level ? $transaction->level->name : 'N/A' }}</span>
                            </div>
                            <div>
                                @if ($transaction->status === 'Approved')
                                    <span class="text-emerald-400 font-medium">Approved</span>
                                @elseif ($transaction->status === 'Paid')
                                    <span class="text-sky-400 font-medium">Paid</span>
                                @elseif ($transaction->status === 'Pending')
                                    <span class="text-amber-400 font-medium">Pending</span>
                                @elseif ($transaction->status === 'Expired')
                                    <span class="text-zinc-500 font-medium">Expired</span>
                                @else
                                    <span class="text-red-400 font-medium">{{ $transaction->status }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-zinc-400">
                            <span>{{ $transaction->created_at->format('M d, Y') }}</span>
                            <span class="font-mono text-zinc-200">
                                Fee: ${{ number_format($transaction->amount, 2) }} | Paid: ${{ number_format($paidAmount, 2) }}
                            </span>
                        </div>

                        @if ($transaction->payments->isNotEmpty())
                            <div class="pt-2 border-t border-zinc-800/70 space-y-1">
                                @foreach ($transaction->payments as $payment)
                                    <div class="flex items-center justify-between text-[11px] text-zinc-400">
                                        <span>
                                            <span class="font-mono text-zinc-300">{{ $payment->payment_reference }}</span>
                                            ({{ $payment->payment_method }} &bull; {{ $payment->payment_date->format('M d, Y') }})
                                        </span>
                                        <span class="font-mono text-emerald-400">+${{ number_format($payment->amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="pt-2.5 border-t border-zinc-800/70 flex flex-wrap items-center justify-between gap-1.5">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <a href="{{ route('transactions.show', $transaction->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                    View Details
                                </a>
                                <a href="{{ route('transactions.receipt', $transaction->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                    Print Receipt
                                </a>
                            </div>
                            @if ($balance > 0 && !in_array($transaction->status, ['Cancelled', 'Expired'], true))
                                <a href="{{ route('payments.create', ['transaction_id' => $transaction->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-emerald-900/60 bg-emerald-950/30 text-emerald-400 hover:bg-emerald-900/40 hover:text-emerald-300 transition-colors">
                                    Record Payment
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
