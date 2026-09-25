<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'capacity',
        'image',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'capacity' => 'integer',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? (str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image)) : null;
    }
}
