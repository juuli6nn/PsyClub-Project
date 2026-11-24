<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    protected $fillable = [
        'membership_number',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'latitude',
        'longitude',
        'is_active',
        'verification_sent_at',
        'membership_category_id',
        'membership_level_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'verification_sent_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MembershipCategory::class, 'membership_category_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_level_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class, 'member_id');
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(MembershipTransaction::class, 'member_id')->latestOfMany();
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            MembershipTransaction::class,
            'member_id',
            'membership_transaction_id'
        );
    }
}
