@extends('layouts.app')

@section('content')
<div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-medium text-white mb-4">Edit Category</h1>

    @if ($errors->any())
        <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Category Name:</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Description:</label>
            <textarea name="description" rows="3" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit" class="bg-white text-black text-xs font-medium px-4 py-2 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                Update Category
            </button>
        </div>
    </form>

    <div class="mt-4 pt-4 border-t border-zinc-850">
        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">&larr; Back to List</a>
    </div>
</div>
@endsection
