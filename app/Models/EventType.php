<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $fillable = ['name', 'slug', 'mode', 'image', 'is_system'];

    public function events()
    {
        return $this->hasMany(Event::class, 'event_type', 'slug');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? (str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image)) : null;
    }
}
