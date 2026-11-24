<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipStatusController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::with(['category', 'level', 'latestTransaction.payments'])->orderBy('first_name');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('membership_number', 'like', '%'.$search.'%');
            });
        }

        $members = $query->get();

        return view('membership-status.index', compact('members'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'membership_status' => 'required|in:Active,Inactive',
        ]);

        $member->update(['is_active' => $validated['membership_status'] === 'Active']);

        return back()->with('success', "Membership status for {$member->first_name} {$member->last_name} saved.");
    }
}
