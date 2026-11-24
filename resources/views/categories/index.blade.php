@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-medium text-white">Membership Categories</h1>
    <a href="{{ route('categories.create') }}" class="bg-white text-black text-xs font-medium px-3 py-1.5 rounded hover:bg-zinc-200 transition-colors">
        Add New Category
    </a>
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
                    <th class="px-4 py-2.5 font-medium">Description</th>
                    <th class="px-4 py-2.5 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                @forelse ($categories as $category)
                <tr class="hover:bg-zinc-900/30">
                    <td class="px-4 py-2.5 text-zinc-400 font-mono text-xs">{{ $category->id }}</td>
                    <td class="px-4 py-2.5 text-white font-medium">{{ $category->name }}</td>
                    <td class="px-4 py-2.5 text-zinc-400 text-xs">{{ $category->description ?? '-' }}</td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-xs">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('categories.edit', $category->id) }}" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this category?')" class="inline-flex items-center px-2.5 py-1 rounded border border-zinc-800 bg-zinc-900 text-zinc-400 hover:border-red-900/60 hover:bg-red-950/40 hover:text-red-400 transition-colors cursor-pointer">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-xs text-zinc-500">
                        No categories found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('members.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900/80 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">&larr; Go to Members</a>
</div>
@endsection
