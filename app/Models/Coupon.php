<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'event_id', 'type', 'value',
        'min_order_amount', 'max_uses', 'used_count', 'max_uses_per_user',
        'starts_at', 'expires_at', 'is_active', 'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'max_uses_per_user' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidForEvent($eventId, $userId = null)
    {
        return $this->validationError($eventId, $userId) === null;
    }

    public function validationError($eventId, $userId = null): ?string
    {
        if (!$this->is_active) return 'Coupon is not active.';

        if ($this->event_id && $this->event_id !== $eventId) return 'This coupon is not valid for this event.';

        if ($this->starts_at && now()->lt($this->starts_at)) return 'Coupon sales have not started yet.';
        if ($this->expires_at && now()->gt($this->expires_at)) return 'Coupon has expired.';

        if ($this->max_uses && $this->used_count >= $this->max_uses) return 'Coupon usage limit has been reached.';

        if ($userId && $this->max_uses_per_user) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->max_uses_per_user) return 'You have already used this coupon.';
        }

        return null;
    }

    public function calculateDiscount($orderAmount)
    {
        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return round($orderAmount * ($this->value / 100), 2);
        }

        return min($this->value, $orderAmount);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeForEvent(Builder $q, $eventId): Builder
    {
        return $q->where(function ($q) use ($eventId) {
            $q->where('event_id', $eventId)->orWhereNull('event_id');
        });
    }
}
