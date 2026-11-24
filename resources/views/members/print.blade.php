@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-4 print:hidden">
        <a href="{{ route('members.show', $member->id) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
            &larr; Back to Member Profile
        </a>
        <button type="button" onclick="window.print()" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
            Print Membership Record
        </button>
    </div>

    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6 sm:p-8 print:bg-white print:text-black print:border-zinc-300">
        <div class="flex items-start justify-between pb-6 mb-6 border-b border-zinc-800 print:border-zinc-300">
            <div>
                <h1 class="text-lg font-medium text-white print:text-black">Official Membership Record</h1>
                <p class="text-xs text-zinc-400 print:text-zinc-600 mt-0.5">Membership Management & Transaction Processing System</p>
            </div>
            <div class="text-right">
                <div class="font-mono text-sm font-medium text-white print:text-black">{{ $member->membership_number }}</div>
                <div class="text-xs {{ $member->is_active ? 'text-emerald-400 print:text-black' : 'text-red-400 print:text-black' }} font-medium mt-0.5">
                    {{ $member->is_active ? 'ACTIVE MEMBER' : 'INACTIVE' }}
                </div>
            </div>
        </div>

        <div class="space-y-2.5 text-xs pb-6 mb-6 border-b border-zinc-800 print:border-zinc-300">
            <div class="flex items-center justify-between py-1">
                <span class="text-zinc-400 print:text-zinc-600">Full Name</span>
                <span class="text-white print:text-black font-medium text-sm">{{ $member->first_name }} {{ $member->last_name }}</span>
            </div>
            <div class="flex items-center justify-between py-1">
                <span class="text-zinc-400 print:text-zinc-600">Email Address</span>
                <span class="text-zinc-200 print:text-black">{{ $member->email }}</span>
            </div>
            <div class="flex items-center justify-between py-1">
                <span class="text-zinc-400 print:text-zinc-600">Phone Number</span>
                <span class="text-zinc-200 print:text-black">{{ $member->phone_number ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between py-1">
                <span class="text-zinc-400 print:text-zinc-600">Membership Category</span>
                <span class="text-zinc-200 print:text-black font-medium">{{ $member->category ? $member->category->name : 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between py-1">
                <span class="text-zinc-400 print:text-zinc-600">Membership Level</span>
                <span class="text-zinc-200 print:text-black font-medium">{{ $member->level ? $member->level->name : 'N/A' }}</span>
            </div>
            @if ($member->level && $member->level->benefits)
            <div class="flex items-start justify-between py-1">
                <span class="text-zinc-400 print:text-zinc-600">Level Benefits</span>
                <span class="text-zinc-300 print:text-zinc-700 text-right max-w-xs">{{ $member->level->benefits }}</span>
            </div>
            @endif
            <div class="flex items-center justify-between py-1">
                <span class="text-zinc-400 print:text-zinc-600">Date Joined</span>
                <span class="text-zinc-200 print:text-black">{{ $member->created_at ? $member->created_at->format('F d, Y') : 'N/A' }}</span>
            </div>
        </div>

        <div>
            <h2 class="text-xs font-medium text-zinc-300 print:text-black mb-3">Transaction Summary</h2>
            @if ($member->transactions->isEmpty())
                <p class="text-xs text-zinc-500">No transactions recorded.</p>
            @else
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-zinc-800 print:border-zinc-300 text-zinc-400 print:text-zinc-600">
                        <tr>
                            <th class="pb-2 font-medium">Reference #</th>
                            <th class="pb-2 font-medium">Type</th>
                            <th class="pb-2 font-medium">Level</th>
                            <th class="pb-2 font-medium">Status</th>
                            <th class="pb-2 font-medium text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-850 print:divide-zinc-200 text-zinc-300 print:text-black">
                        @foreach ($member->transactions as $transaction)
                            <tr>
                                <td class="py-2 font-mono">{{ $transaction->transaction_number }}</td>
                                <td class="py-2 capitalize">{{ $transaction->type }}</td>
                                <td class="py-2">{{ $transaction->level ? $transaction->level->name : 'N/A' }}</td>
                                <td class="py-2">{{ $transaction->status }}</td>
                                <td class="py-2 text-right font-mono">${{ number_format($transaction->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-8 pt-4 border-t border-zinc-850 print:border-zinc-300 text-center text-[11px] text-zinc-500">
            Printed on {{ now()->format('F d, Y h:i A') }} &bull; Official Membership Record
        </div>
    </div>
</div>
@endsection
