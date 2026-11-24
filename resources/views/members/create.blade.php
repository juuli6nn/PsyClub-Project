@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('members.index') }}" class="inline-flex items-center px-3 py-1.5 rounded border border-zinc-800 bg-zinc-900 text-zinc-200 text-xs hover:bg-zinc-800 hover:border-zinc-700 hover:text-white transition-colors">
            &larr; Back to List
        </a>
    </div>

    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6">
    <h1 class="text-xl font-medium text-white mb-4">Add New Member</h1>

    @if ($errors->any())
        <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('members.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">First Name:</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Last Name:</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Phone Number:</label>
            <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Membership Category:</label>
            <select name="membership_category_id" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('membership_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-300 mb-1">Membership Level:</label>
            <select name="membership_level_id" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                <option value="">-- Select Level --</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" {{ old('membership_level_id') == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit" class="bg-white text-black text-xs font-medium px-4 py-2 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                Save Member
            </button>
        </div>
    </form>

    </div>
</div>
@endsection
