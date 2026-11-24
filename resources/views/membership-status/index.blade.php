@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-medium text-white">Membership status</h1>
    <p class="text-xs text-zinc-400 mt-0.5">Update each member's membership status. Payment information is shown in the transaction history.</p>
</div>

<div class="mb-4">
    <form action="{{ route('membership-status.index') }}" method="GET" class="flex items-center gap-1.5">
        <input type="text" name="search" placeholder="Search name, email, or membership number..." value="{{ request('search') }}" class="bg-zinc-900 border border-zinc-800 text-white text-xs rounded px-3 py-1.5 focus:outline-none focus:border-zinc-600 w-72 placeholder-zinc-500">
        <button type="submit" class="bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs font-medium px-3.5 py-1.5 rounded hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors cursor-pointer">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('membership-status.index') }}" class="bg-zinc-900/60 border border-zinc-800 text-zinc-400 text-xs font-medium px-3 py-1.5 rounded hover:text-white hover:bg-zinc-800 transition-colors">
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

@if ($errors->any())
    <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="border border-zinc-800 rounded-lg bg-zinc-950 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-800 bg-zinc-900/40 text-xs text-zinc-400">
                <tr>
                    <th class="px-4 py-2.5 font-medium">Member</th>
                    <th class="px-4 py-2.5 font-medium">Membership Number</th>
                    <th class="px-4 py-2.5 font-medium">Category / Level</th>
                    <th class="px-4 py-2.5 font-medium">Latest Transaction</th>
                    <th class="px-4 py-2.5 font-medium">Membership status</th>
                    <th class="px-4 py-2.5 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                @forelse ($members as $member)
                    @php
                        $transaction = $member->latestTransaction;
                        $formId = 'status-form-'.$member->id;
                    @endphp
                    <tr class="hover:bg-zinc-900/30">
                        <td class="px-4 py-2.5">
                            <a href="{{ route('members.show', $member->id) }}" class="text-white font-medium hover:underline underline-offset-2">
                                {{ $member->first_name }} {{ $member->last_name }}
                            </a>
                            <div class="text-xs text-zinc-500">{{ $member->email }}</div>
                        </td>
                        <td class="px-4 py-2.5 font-mono text-xs text-zinc-300">{{ $member->membership_number }}</td>
                        <td class="px-4 py-2.5 text-xs">
                            {{ $member->category ? $member->category->name : 'N/A' }}
                            <span class="text-zinc-600">/</span>
                            {{ $member->level ? $member->level->name : 'N/A' }}
                        </td>
                        <td class="px-4 py-2.5 text-xs">
                            @if ($transaction)
                                <a href="{{ route('transactions.show', $transaction->id) }}" class="font-mono text-zinc-300 hover:underline underline-offset-2">{{ $transaction->transaction_number }}</a>
                                <div class="{{ $transaction->status === 'Approved' ? 'text-emerald-400' : ($transaction->status === 'Pending' ? 'text-amber-400' : 'text-zinc-500') }}">
                                    {{ $transaction->status }}
                                </div>
                            @else
                                <span class="text-zinc-500">None</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="relative w-32 mr-4">
                                <select form="{{ $formId }}" name="membership_status" class="w-full appearance-none bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs rounded px-2.5 py-1.5 pr-10 focus:outline-none focus:border-zinc-600">
                                    <option value="Active" @selected($member->is_active)>Active</option>
                                    <option value="Inactive" @selected(! $member->is_active)>Inactive</option>
                                </select>
                                <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-300" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <form id="{{ $formId }}" action="{{ route('membership-status.update', $member->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center px-3 py-1 rounded border border-zinc-800 bg-zinc-900 text-xs text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors cursor-pointer">
                                    Save
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-xs text-zinc-500">
                            No members found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
