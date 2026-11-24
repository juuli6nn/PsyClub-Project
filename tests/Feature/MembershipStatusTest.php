<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeMemberWithTransaction(string $status = 'Pending'): array
{
    $category = MembershipCategory::create(['name' => 'Regular']);
    $level = MembershipLevel::create(['name' => 'Gold', 'fee' => 100.00, 'validity_months' => 12]);

    $member = Member::create([
        'membership_number' => 'MEM-STAT01',
        'first_name' => 'Nagi',
        'last_name' => 'Seishiro',
        'email' => 'nagi.status@example.com',
        'is_active' => true,
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    $transaction = MembershipTransaction::create([
        'transaction_number' => 'TXN-STATUS-0001',
        'member_id' => $member->id,
        'membership_level_id' => $level->id,
        'type' => 'application',
        'amount' => 100.00,
        'status' => $status,
    ]);

    return [$member, $transaction];
}

test('membership status page lists members for the administrator', function () {
    $admin = User::factory()->create();
    makeMemberWithTransaction();

    $this->actingAs($admin)
        ->get(route('membership-status.index'))
        ->assertStatus(200)
        ->assertSee('Nagi');
});

test('updating a member status changes only the membership status', function () {
    $admin = User::factory()->create();
    [$member, $transaction] = makeMemberWithTransaction();

    $this->actingAs($admin)
        ->put(route('membership-status.update', $member->id), [
            'membership_status' => 'Inactive',
        ])
        ->assertSessionHasNoErrors();

    expect($transaction->fresh()->status)->toBe('Pending')
        ->and($transaction->payments()->count())->toBe(0)
        ->and($member->fresh()->is_active)->toBeFalse();
});

test('membership status update does not change an approved transaction', function () {
    $admin = User::factory()->create();
    [$member, $transaction] = makeMemberWithTransaction('Approved');

    $this->actingAs($admin)
        ->put(route('membership-status.update', $member->id), [
            'membership_status' => 'Active',
        ])
        ->assertSessionHasNoErrors();

    expect($transaction->fresh()->status)->toBe('Approved');
});
