<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // Start building the query with the relationships
        $query = Member::with(['category', 'level'])->latest();

        // Check if a search term was submitted
        if ($request->filled('search')) {
            $search = $request->search;

            // Group the search conditions so they don't interfere with other queries
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhereHas('category', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('level', function ($q3) use ($search) {
                        $q3->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('membership_category_id', $request->category);
        }

        if ($request->filled('level')) {
            $query->where('membership_level_id', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $members = $query->get();
        $categories = MembershipCategory::orderBy('name')->get();
        $levels = MembershipLevel::orderBy('fee')->get();

        return view('members.index', compact('members', 'categories', 'levels'));
    }

    public function create()
    {
        // Fetch categories and levels to pass to the dropdowns in the form
        $categories = MembershipCategory::all();
        $levels = MembershipLevel::all();

        return view('members.create', compact('categories', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'membership_number' => 'nullable|string|max:50|unique:members,membership_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone_number' => 'nullable|string|max:20',
            'membership_category_id' => 'required|exists:membership_categories,id',
            'membership_level_id' => 'required|exists:membership_levels,id',
        ]);

        // Fulfill the requirement to create a unique membership number
        if (empty($validated['membership_number'])) {
            do {
                $membershipNumber = 'MEM-'.strtoupper(Str::random(6));
            } while (Member::where('membership_number', $membershipNumber)->exists());

            $validated['membership_number'] = $membershipNumber;
        }

        $validated['is_active'] = true;

        $member = Member::create($validated);

        $level = MembershipLevel::find($member->membership_level_id);

        MembershipTransaction::create([
            'transaction_number' => 'TXN-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'member_id' => $member->id,
            'membership_level_id' => $member->membership_level_id,
            'type' => 'application',
            'amount' => $level ? $level->fee : 0,
            'status' => 'Pending',
            'notes' => 'Initial membership application.',
        ]);

        return redirect()->route('members.index')->with('success', 'Member created and initial application transaction generated.');
    }

    public function edit(Member $member)
    {
        // Fetch categories and levels to pre-select the current ones in the dropdowns
        $categories = MembershipCategory::all();
        $levels = MembershipLevel::all();

        return view('members.edit', compact('member', 'categories', 'levels'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,'.$member->id,
            'phone_number' => 'nullable|string|max:20',
            'membership_category_id' => 'required|exists:membership_categories,id',
            'membership_level_id' => 'required|exists:membership_levels,id',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    public function show(Member $member)
    {
        $member->load([
            'category',
            'level',
            'transactions' => function ($query) {
                $query->with(['level', 'payments'])->latest();
            },
        ]);

        return view('members.show', compact('member'));
    }

    public function print(Member $member)
    {
        $member->load([
            'category',
            'level',
            'transactions' => function ($query) {
                $query->with(['level', 'payments'])->latest();
            },
        ]);

        return view('members.print', compact('member'));
    }

    public function toggleStatus(Member $member)
    {
        $member->update([
            'is_active' => ! $member->is_active,
        ]);

        $statusText = $member->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Member {$statusText} successfully.");
    }
}
