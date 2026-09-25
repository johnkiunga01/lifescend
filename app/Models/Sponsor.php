<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'logo',
        'image',
        'website',
        'description',
        'level',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getImageUrlAttribute()
    {
        $src = $this->image ?? $this->logo;
        return $src ? (str_starts_with($src, 'http') ? $src : asset('storage/' . $src)) : null;
    }
}
