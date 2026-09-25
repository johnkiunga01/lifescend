<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'email',
        'phone',
        'bio',
        'company',
        'job_title',
        'speak_time',
        'photo',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'speaker_event')
            ->withPivot('featured')
            ->withTimestamps();
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'session_speaker')
            ->withTimestamps();
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? (str_starts_with($this->photo, 'http') ? $this->photo : asset('storage/' . $this->photo)) : asset('images/profile_placeholder.png');
    }
}
