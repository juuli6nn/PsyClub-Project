<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'name' => 'Administrator',
                'first_name' => 'Arianne',
                'last_name' => 'Julian',
                'street_address' => 'Taft Avenue, Ermita',
                'city' => 'Manila',
                'province' => 'Metro Manila',
                'country_code' => 'PH',
                'postal_code' => '1000',
                'latitude' => 14.5995,
                'longitude' => 120.9842,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'profile_completed_at' => now(),
            ]
        );

        $categories = [
            [
                'name' => 'Student',
                'level' => 'Associate',
                'fee' => 250.00,
                'description' => 'Discounted membership for currently enrolled students.',
            ],
            [
                'name' => 'Regular',
                'level' => 'Standard',
                'fee' => 500.00,
                'description' => 'Standard individual membership for adults.',
            ],
            [
                'name' => 'Senior Citizen',
                'level' => 'Priority',
                'fee' => 200.00,
                'description' => 'Special membership rate for senior citizens aged 60 and above.',
            ],
            [
                'name' => 'Corporate',
                'level' => 'Executive',
                'fee' => 1000.00,
                'description' => 'Company-sponsored membership for corporate employees and partners.',
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $category) {
            $cat = MembershipCategory::updateOrCreate(
                ['name' => $category['name']],
                [
                    'level' => $category['level'],
                    'fee' => $category['fee'],
                    'is_active' => true,
                    'description' => $category['description'],
                ]
            );
            $categoryMap[$category['name']] = $cat;
        }

        $levels = [
            [
                'name' => 'Basic',
                'fee' => 25.00,
                'validity_months' => 6,
                'benefits' => 'Standard access, digital membership card, monthly newsletter.',
            ],
            [
                'name' => 'Silver',
                'fee' => 50.00,
                'validity_months' => 12,
                'benefits' => 'All Basic benefits plus 5% discount on partner services and event access.',
            ],
            [
                'name' => 'Gold',
                'fee' => 100.00,
                'validity_months' => 12,
                'benefits' => 'All Silver benefits plus 15% discount, priority support, and free guest pass.',
            ],
            [
                'name' => 'Platinum',
                'fee' => 200.00,
                'validity_months' => 24,
                'benefits' => 'All Gold benefits plus VIP lounge access, 25% discount, and dedicated account manager.',
            ],
        ];

        $levelMap = [];
        foreach ($levels as $level) {
            $lvl = MembershipLevel::firstOrCreate(
                ['name' => $level['name']],
                [
                    'fee' => $level['fee'],
                    'validity_months' => $level['validity_months'],
                    'benefits' => $level['benefits'],
                ]
            );
            $levelMap[$level['name']] = $lvl;
        }

        // Seed sample members with transactions and payments if they don't exist yet
        $sampleMembers = [
            [
                'email' => 'nagi.seishiro@example.com',
                'first_name' => 'Nagi',
                'last_name' => 'Seishiro',
                'phone_number' => '0917-100-2000',
                'category' => 'Student',
                'level' => 'Gold',
                'txn_status' => 'Approved',
                'payment_method' => 'GCash',
            ],
            [
                'email' => 'reo.mikage@example.com',
                'first_name' => 'Reo',
                'last_name' => 'Mikage',
                'phone_number' => '0917-100-2001',
                'category' => 'Corporate',
                'level' => 'Platinum',
                'txn_status' => 'Approved',
                'payment_method' => 'Credit Card',
            ],
            [
                'email' => 'isagi.yoichi@example.com',
                'first_name' => 'Isagi',
                'last_name' => 'Yoichi',
                'phone_number' => '0917-100-2002',
                'category' => 'Student',
                'level' => 'Silver',
                'txn_status' => 'Paid',
                'payment_method' => 'GCash',
            ],
            [
                'email' => 'bachira.meguru@example.com',
                'first_name' => 'Bachira',
                'last_name' => 'Meguru',
                'phone_number' => '0917-100-2003',
                'category' => 'Regular',
                'level' => 'Gold',
                'txn_status' => 'Pending',
                'payment_method' => null,
            ],
        ];

        foreach ($sampleMembers as $sample) {
            $level = $levelMap[$sample['level']];
            $category = $categoryMap[$sample['category']];

            $member = Member::firstOrCreate(
                ['email' => $sample['email']],
                [
                    'membership_number' => 'MEM-'.strtoupper(Str::random(6)),
                    'first_name' => $sample['first_name'],
                    'last_name' => $sample['last_name'],
                    'phone_number' => $sample['phone_number'],
                    'is_active' => true,
                    'membership_category_id' => $category->id,
                    'membership_level_id' => $level->id,
                ]
            );

            if ($member->transactions()->count() === 0) {
                $transaction = MembershipTransaction::create([
                    'transaction_number' => 'TXN-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                    'member_id' => $member->id,
                    'membership_level_id' => $level->id,
                    'type' => 'application',
                    'amount' => $level->fee,
                    'status' => $sample['txn_status'],
                    'notes' => 'Initial membership application.',
                ]);

                if ($sample['payment_method']) {
                    Payment::create([
                        'payment_reference' => 'PAY-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                        'membership_transaction_id' => $transaction->id,
                        'amount' => $level->fee,
                        'payment_method' => $sample['payment_method'],
                        'payment_date' => now()->toDateString(),
                        'notes' => 'Full membership fee settlement.',
                    ]);
                }
            }
        }

        // Ensure any previously created members without transactions also have an initial transaction
        $membersWithoutTxns = Member::doesntHave('transactions')->with('level')->get();
        foreach ($membersWithoutTxns as $existingMember) {
            $lvl = $existingMember->level ?? $levelMap['Basic'];
            MembershipTransaction::create([
                'transaction_number' => 'TXN-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                'member_id' => $existingMember->id,
                'membership_level_id' => $lvl->id,
                'type' => 'application',
                'amount' => $lvl->fee,
                'status' => 'Pending',
                'notes' => 'Initial membership application.',
            ]);
        }
    }
}
