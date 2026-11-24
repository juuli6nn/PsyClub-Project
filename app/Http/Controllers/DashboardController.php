<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = Member::count();
        $activeMembers = Member::where('is_active', true)->count();
        $pendingTransactions = MembershipTransaction::where('status', 'Pending')->count();
        $approvedTransactions = MembershipTransaction::where('status', 'Approved')->count();
        $collectedPayments = (float) Payment::sum('amount');
        $totalPaymentsCount = Payment::count();

        $recentTransactions = MembershipTransaction::with(['member', 'level'])
            ->latest()
            ->limit(6)
            ->get();

        $levelsBreakdown = MembershipLevel::withCount('members')
            ->orderByDesc('members_count')
            ->get();

        return view('welcome', compact(
            'totalMembers',
            'activeMembers',
            'pendingTransactions',
            'approvedTransactions',
            'collectedPayments',
            'totalPaymentsCount',
            'recentTransactions',
            'levelsBreakdown'
        ));
    }
}
