<?php

namespace App\Http\Controllers;

use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['transaction.member', 'transaction.level'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', '%'.$search.'%')
                    ->orWhere('payment_method', 'like', '%'.$search.'%')
                    ->orWhereHas('transaction', function ($q2) use ($search) {
                        $q2->where('transaction_number', 'like', '%'.$search.'%')
                            ->orWhereHas('member', function ($q3) use ($search) {
                                $q3->where('first_name', 'like', '%'.$search.'%')
                                    ->orWhere('last_name', 'like', '%'.$search.'%');
                            });
                    });
            });
        }

        $payments = $query->get();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $selectedTransaction = null;
        $remainingBalance = null;

        if ($request->filled('transaction_id')) {
            $selectedTransaction = MembershipTransaction::with(['member', 'level', 'payments'])
                ->findOrFail($request->query('transaction_id'));

            $totalPaid = $selectedTransaction->payments->sum('amount');
            $remainingBalance = max(0, (float) $selectedTransaction->amount - (float) $totalPaid);
        }

        $transactions = MembershipTransaction::with(['member', 'level', 'payments'])
            ->whereNotIn('status', ['Cancelled', 'Expired'])
            ->latest()
            ->get();

        return view('payments.create', compact('transactions', 'selectedTransaction', 'remainingBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'membership_transaction_id' => 'required|exists:membership_transactions,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,GCash,Bank Transfer,Credit Card',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $transaction = MembershipTransaction::with('payments')->findOrFail($validated['membership_transaction_id']);

        if (in_array($transaction->status, ['Cancelled', 'Expired'], true)) {
            return redirect()->back()->withInput()->withErrors([
                'membership_transaction_id' => "Cannot record payment for a {$transaction->status} transaction.",
            ]);
        }

        $totalPaidBefore = (float) $transaction->payments->sum('amount');
        $remainingBalance = max(0, (float) $transaction->amount - $totalPaidBefore);

        if ($remainingBalance <= 0) {
            return redirect()->back()->withInput()->withErrors([
                'amount' => 'This transaction has already been paid in full.',
            ]);
        }

        if ((float) $validated['amount'] > $remainingBalance) {
            return redirect()->back()->withInput()->withErrors([
                'amount' => 'Payment amount cannot exceed the remaining balance of $'.number_format($remainingBalance, 2).'.',
            ]);
        }

        Payment::create([
            'payment_reference' => 'PAY-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'membership_transaction_id' => $transaction->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $newTotalPaid = $totalPaidBefore + (float) $validated['amount'];

        if ($newTotalPaid >= (float) $transaction->amount && $transaction->status === 'Pending') {
            $transaction->update([
                'status' => 'Paid',
            ]);
        }

        return redirect()->route('transactions.show', $transaction->id)
            ->with('success', 'Payment recorded successfully.');
    }
}
