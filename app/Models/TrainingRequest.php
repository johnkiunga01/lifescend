<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'title',
        'slug',
        'description',
        'training_type',
        'event_type',
        'venue_id',
        'estimated_attendees',
        'preferred_date',
        'start_date',
        'end_date',
        'timezone',
        'cover_image',
        'capacity',
        'payment_type',
        'topics',
        'keynotes',
        'sponsor_ids',
        'pricing',
        'status',
        'visibility',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'estimated_attendees' => 'integer',
        'capacity' => 'integer',
        'topics' => 'array',
        'keynotes' => 'array',
        'sponsor_ids' => 'array',
        'pricing' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->slug)) {
                $request->slug = Str::slug($request->title) . '-' . Str::random(4);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
