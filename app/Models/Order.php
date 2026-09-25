<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'total_amount',
        'discount_amount',
        'currency',
        'status',
        'stripe_payment_intent_id',
        'coupon_id',
        'reservation_type',
        'company_name',
        'company_details',
        'contact_person',
        'attendee_count',
        'payment_method',
        'reserved_at',
        'paid_at',
        'paid_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'attendee_count' => 'integer',
        'reserved_at' => 'datetime',
        'paid_at' => 'datetime',
        'paid_amount' => 'decimal:2',
    ];

    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }

    public function isPaid(): bool
    {
        return $this->status === 'completed';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
