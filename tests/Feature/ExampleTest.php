<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('authenticated administrator can view the dashboard', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Collected Payments');
});

test('registering a member creates a unique membership number and initial pending transaction', function () {
    $admin = User::factory()->create();
    $category = MembershipCategory::create(['name' => 'Student', 'description' => 'Student category']);
    $level = MembershipLevel::create(['name' => 'Gold', 'fee' => 100.00, 'validity_months' => 12]);

    $response = $this->actingAs($admin)->post(route('members.store'), [
        'first_name' => 'Nagi',
        'last_name' => 'Seishiro',
        'email' => 'nagi@example.com',
        'phone_number' => '09170001111',
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    $response->assertRedirect(route('members.index'));

    $member = Member::where('email', 'nagi@example.com')->first();
    expect($member)->not->toBeNull()
        ->and($member->membership_number)->toStartWith('MEM-')
        ->and($member->is_active)->toBeTrue();

    $transaction = MembershipTransaction::where('member_id', $member->id)->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->status)->toBe('Pending')
        ->and((float) $transaction->amount)->toBe(100.00);
});

test('administrator can deactivate and reactivate a member', function () {
    $admin = User::factory()->create();
    $category = MembershipCategory::create(['name' => 'Regular']);
    $level = MembershipLevel::create(['name' => 'Basic', 'fee' => 25.00, 'validity_months' => 6]);

    $member = Member::create([
        'membership_number' => 'MEM-TEST01',
        'first_name' => 'Reo',
        'last_name' => 'Mikage',
        'email' => 'reo@example.com',
        'is_active' => true,
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    $this->actingAs($admin)->patch(route('members.toggle-status', $member->id));
    expect($member->fresh()->is_active)->toBeFalse();

    $this->actingAs($admin)->patch(route('members.toggle-status', $member->id));
    expect($member->fresh()->is_active)->toBeTrue();
});

test('unpaid transactions cannot be marked as approved until paid in full', function () {
    $admin = User::factory()->create();
    $category = MembershipCategory::create(['name' => 'Corporate']);
    $level = MembershipLevel::create(['name' => 'Platinum', 'fee' => 200.00, 'validity_months' => 24]);

    $member = Member::create([
        'membership_number' => 'MEM-TEST02',
        'first_name' => 'Isagi',
        'last_name' => 'Yoichi',
        'email' => 'isagi@example.com',
        'is_active' => true,
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    $transaction = MembershipTransaction::create([
        'transaction_number' => 'TXN-TEST-0001',
        'member_id' => $member->id,
        'membership_level_id' => $level->id,
        'type' => 'application',
        'amount' => 200.00,
        'status' => 'Pending',
    ]);

    // Try approving while still Pending
    $this->actingAs($admin)
        ->patch(route('transactions.update-status', $transaction->id), ['status' => 'Approved'])
        ->assertSessionHasErrors('status');

    expect($transaction->fresh()->status)->toBe('Pending');

    // Record full payment -> should automatically move status from Pending to Paid
    $this->actingAs($admin)
        ->post(route('payments.store'), [
            'membership_transaction_id' => $transaction->id,
            'amount' => 200.00,
            'payment_method' => 'GCash',
            'payment_date' => now()->toDateString(),
            'notes' => 'Full payment',
        ])
        ->assertRedirect(route('transactions.show', $transaction->id));

    expect($transaction->fresh()->status)->toBe('Paid');

    // Now approving the Paid transaction succeeds
    $this->actingAs($admin)
        ->patch(route('transactions.update-status', $transaction->id), ['status' => 'Approved'])
        ->assertSessionHasNoErrors();

    expect($transaction->fresh()->status)->toBe('Approved');

    // Printable receipt and member record pages load cleanly
    $this->actingAs($admin)->get(route('transactions.receipt', $transaction->id))->assertStatus(200);
    $this->actingAs($admin)->get(route('members.print', $member->id))->assertStatus(200);
});
