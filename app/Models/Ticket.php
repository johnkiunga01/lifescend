<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'order_id',
        'ticket_type_id',
        'qr_code',
        'status',
        'checked_in_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function attendee()
    {
        return $this->hasOne(Attendee::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }
}
