@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-medium text-white">Members List</h1>
    <a href="{{ route('members.create') }}" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors">
        Add New Member
    </a>
</div>

<!-- Styled Search & Filter Form -->
<div class="mb-4">
    <form action="{{ route('members.index') }}" method="GET" class="flex flex-wrap items-center gap-1.5">
        <input type="text" name="search" placeholder="Search name, category, or level..." value="{{ request('search') }}" class="bg-zinc-900 border border-zinc-800 text-white text-xs rounded px-3 py-1.5 focus:outline-none focus:border-zinc-600 w-72 placeholder-zinc-500">

        <div class="relative">
            <select name="category" class="appearance-none bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs rounded px-2.5 py-1.5 pr-9 focus:outline-none focus:border-zinc-600">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-300" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>

        <div class="relative">
            <select name="level" class="appearance-none bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs rounded px-2.5 py-1.5 pr-9 focus:outline-none focus:border-zinc-600">
                <option value="">All levels</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" @selected(request('level') == $level->id)>{{ $level->name }}</option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-300" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>

        <div class="relative">
            <select name="status" class="appearance-none bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs rounded px-2.5 py-1.5 pr-9 focus:outline-none focus:border-zinc-600">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <svg class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-300" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>

        <button type="submit" class="bg-zinc-900 border border-zinc-800 text-zinc-200 text-xs font-medium px-3.5 py-1.5 rounded hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors cursor-pointer">
            Search
        </button>
        @if(request('search') || request('category') || request('level') || request('status'))
            <a href="{{ route('members.index') }}" class="bg-zinc-900/60 border border-zinc-800 text-zinc-400 text-xs font-medium px-3 py-1.5 rounded hover:text-white hover:bg-zinc-800 transition-colors">
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
                    <th class="px-4 py-2.5 font-medium">ID</th>
                    <th class="px-4 py-2.5 font-medium">Name</th>
                    <th class="px-4 py-2.5 font-medium">Email</th>
                    <th class="px-4 py-2.5 font-medium">Membership Number</th>
                    <th class="px-4 py-2.5 font-medium">Category</th>
                    <th class="px-4 py-2.5 font-medium">Level</th>
                    <th class="px-4 py-2.5 font-medium">Status</th>
                    <th class="px-4 py-2.5 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                @forelse ($members as $member)
                <tr class="hover:bg-zinc-900/30">
                    <td class="px-4 py-2.5 text-zinc-400 font-mono text-xs">{{ $member->id }}</td>
                    <td class="px-4 py-2.5 text-white font-medium">
                        <a href="{{ route('members.show', $member->id) }}" class="hover:underline underline-offset-2">
                            {{ $member->first_name }} {{ $member->last_name }}
                        </a>
                    </td>
                    <td class="px-4 py-2.5 text-zinc-400">{{ $member->email }}</td>
                    <td class="px-4 py-2.5 font-mono text-xs text-zinc-300">{{ $member->membership_number }}</td>
                    <td class="px-4 py-2.5">{{ $member->category ? $member->category->name : 'N/A' }}</td>
                    <td class="px-4 py-2.5">{{ $member->level ? $member->level->name : 'N/A' }}</td>
                    <td class="px-4 py-2.5 text-xs">
                        @if ($member->is_active)
                            <span class="text-emerald-400 font-medium">Active</span>
                        @else
                            <span class="text-red-400 font-medium">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-xs">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('members.show', $member->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                View
                            </a>
                            <a href="{{ route('members.edit', $member->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('members.toggle-status', $member->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-300 {{ $member->is_active ? 'hover:border-amber-900/60 hover:bg-amber-950/40 hover:text-amber-400' : 'hover:border-emerald-900/60 hover:bg-emerald-950/40 hover:text-emerald-400' }} transition-colors cursor-pointer">
                                    {{ $member->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form action="{{ route('members.destroy', $member->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this member?')" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-400 hover:border-red-900/60 hover:bg-red-950/40 hover:text-red-400 transition-colors cursor-pointer">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-xs text-zinc-500">
                        No members found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 flex flex-wrap items-center gap-2">
    <a href="{{ route('categories.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900/80 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
        Manage Categories
    </a>
    <a href="{{ route('levels.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900/80 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
        Manage Levels
    </a>
</div>
@endsection
