<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'room',
        'capacity',
        'track_id',
        'session_type_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'capacity' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function sessionType()
    {
        return $this->belongsTo(SessionType::class);
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'session_speaker')
            ->withTimestamps();
    }
}
