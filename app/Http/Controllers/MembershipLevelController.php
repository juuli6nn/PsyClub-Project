<?php

namespace App\Http\Controllers;

use App\Models\MembershipLevel;
use Illuminate\Http\Request;

class MembershipLevelController extends Controller
{
    public function index()
    {
        $levels = MembershipLevel::latest()->get();

        return view('levels.index', compact('levels'));
    }

    public function create()
    {
        return view('levels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:membership_levels,name',
            'fee' => 'required|numeric|min:0',
            'validity_months' => 'required|integer|min:1',
            'benefits' => 'nullable|string',
        ]);

        MembershipLevel::create($validated);

        return redirect()->route('levels.index')->with('success', 'Level created successfully.');
    }

    public function edit(MembershipLevel $level)
    {
        return view('levels.edit', compact('level'));
    }

    public function update(Request $request, MembershipLevel $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:membership_levels,name,'.$level->id,
            'fee' => 'required|numeric|min:0',
            'validity_months' => 'required|integer|min:1',
            'benefits' => 'nullable|string',
        ]);

        $level->update($validated);

        return redirect()->route('levels.index')->with('success', 'Level updated successfully.');
    }

    public function destroy(MembershipLevel $level)
    {
        $level->delete();

        return redirect()->route('levels.index')->with('success', 'Level deleted successfully.');
    }
}
