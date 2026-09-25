<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SavedEvent extends Pivot
{
    protected $table = 'saved_events';

    protected $fillable = [
        'user_id',
        'event_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
