<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'payment_reference',
        'membership_transaction_id',
        'amount',
        'payment_method',
        'payment_date',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(MembershipTransaction::class, 'membership_transaction_id');
    }
}
