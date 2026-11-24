@extends('layouts.app')

@section('content')
<div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-medium text-white mb-4">Create Transaction</h1>

    @if ($errors->any())
        <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Member:</label>
            <select name="member_id" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                <option value="">-- Select Member --</option>
                @foreach ($members as $member)
                    <option value="{{ $member->id }}" {{ old('member_id', $selectedMemberId) == $member->id ? 'selected' : '' }}>
                        {{ $member->first_name }} {{ $member->last_name }} ({{ $member->membership_number }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Transaction Type:</label>
            <select name="type" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                <option value="application" {{ old('type', $selectedType) === 'application' ? 'selected' : '' }}>New Application</option>
                <option value="renewal" {{ old('type', $selectedType) === 'renewal' ? 'selected' : '' }}>Membership Renewal</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Membership Level:</label>
            <select name="membership_level_id" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                <option value="">-- Select Level --</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" {{ old('membership_level_id') == $level->id ? 'selected' : '' }}>
                        {{ $level->name }} (${{ number_format($level->fee, 2) }} / {{ $level->validity_months }} months)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Notes (Optional):</label>
            <textarea name="notes" rows="3" placeholder="Additional transaction notes..." class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">{{ old('notes') }}</textarea>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit" class="bg-white text-black text-xs font-medium px-4 py-2 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                Create Transaction
            </button>
        </div>
    </form>

    <div class="mt-4 pt-4 border-t border-zinc-850">
        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">&larr; Back to Transactions</a>
    </div>
</div>
@endsection
