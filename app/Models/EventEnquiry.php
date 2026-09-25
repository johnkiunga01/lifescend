<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEnquiry extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'contact',
        'company',
        'message',
        'reply_message',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attachments()
    {
        return $this->hasMany(EventEnquiryAttachment::class);
    }
}
