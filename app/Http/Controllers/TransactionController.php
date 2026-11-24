<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipTransaction::with(['member', 'level'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', '%'.$search.'%')
                    ->orWhereHas('member', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%')
                            ->orWhere('membership_number', 'like', '%'.$search.'%');
                    });
            });
        }

        $transactions = $query->get();

        return view('transactions.index', compact('transactions'));
    }

    public function create(Request $request)
    {
        $members = Member::with('level')->orderBy('first_name')->get();
        $levels = MembershipLevel::all();
        $selectedMemberId = $request->query('member_id');
        $selectedType = $request->query('type', 'application');

        return view('transactions.create', compact('members', 'levels', 'selectedMemberId', 'selectedType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'membership_level_id' => 'required|exists:membership_levels,id',
            'type' => 'required|in:application,renewal',
            'notes' => 'nullable|string',
        ]);

        $level = MembershipLevel::findOrFail($validated['membership_level_id']);

        $transaction = MembershipTransaction::create([
            'transaction_number' => 'TXN-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'member_id' => $validated['member_id'],
            'membership_level_id' => $level->id,
            'type' => $validated['type'],
            'amount' => $level->fee,
            'status' => 'Pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('transactions.show', $transaction->id)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(MembershipTransaction $transaction)
    {
        $transaction->load(['member.category', 'level', 'payments']);

        return view('transactions.show', compact('transaction'));
    }

    public function receipt(MembershipTransaction $transaction)
    {
        $transaction->load(['member.category', 'level', 'payments']);

        return view('transactions.receipt', compact('transaction'));
    }

    public function renew(Member $member)
    {
        $level = $member->level ?? MembershipLevel::first();

        if (! $level) {
            return redirect()->back()->withErrors(['level' => 'Please create a membership level before renewing.']);
        }

        $transaction = MembershipTransaction::create([
            'transaction_number' => 'TXN-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
            'type' => 'renewal',
            'amount' => $level->fee,
            'status' => 'Pending',
            'notes' => 'Membership renewal for '.$level->name.' level.',
        ]);

        return redirect()->route('transactions.show', $transaction->id)
            ->with('success', 'Renewal transaction generated successfully.');
    }

    public function updateStatus(Request $request, MembershipTransaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Paid,Approved,Expired,Cancelled',
        ]);

        if ($validated['status'] === 'Approved' && $transaction->status !== 'Paid') {
            return redirect()->back()->withErrors([
                'status' => 'Only paid transactions may be marked as approved.',
            ]);
        }

        $transaction->update([
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'Approved' && $transaction->member) {
            $transaction->member->update([
                'is_active' => true,
                'membership_level_id' => $transaction->membership_level_id ?? $transaction->member->membership_level_id,
            ]);
        }

        return redirect()->back()->with('success', "Transaction status updated to {$validated['status']}.");
    }
}
