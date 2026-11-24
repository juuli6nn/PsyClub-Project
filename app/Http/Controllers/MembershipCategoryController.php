<?php

namespace App\Http\Controllers;

use App\Models\MembershipCategory;
use Illuminate\Http\Request;

class MembershipCategoryController extends Controller
{
    public function index()
    {
        $categories = MembershipCategory::latest()->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:membership_categories,name',
            'description' => 'nullable|string',
        ]);

        MembershipCategory::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(MembershipCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, MembershipCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:membership_categories,name,'.$category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(MembershipCategory $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
