<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'price_eur',
        'discount_price',
        'discount_price_eur',
        'currency',
        'description',
        'highlights',
        'quantity',
        'sold_count',
        'max_per_user',
        'sales_start',
        'sales_end',
        'is_active',
        'is_paid',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_eur' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_price_eur' => 'decimal:2',
        'quantity' => 'integer',
        'sold_count' => 'integer',
        'max_per_user' => 'integer',
        'highlights' => 'array',
        'sales_start' => 'datetime',
        'sales_end' => 'datetime',
        'is_active' => 'boolean',
        'is_paid' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function checkoutCurrency(): string
    {
        return $this->price_eur !== null ? 'EUR' : $this->currency;
    }

    public function regularPrice(): float
    {
        return (float) ($this->price_eur ?? $this->price);
    }

    public function payablePrice(): float
    {
        return (float) ($this->price_eur !== null
            ? ($this->discount_price_eur ?? $this->price_eur)
            : ($this->discount_price ?? $this->price));
    }

    public function usdReferencePrice(): ?float
    {
        return $this->price_eur !== null && $this->price > 0
            ? (float) ($this->discount_price ?? $this->price)
            : null;
    }
}
