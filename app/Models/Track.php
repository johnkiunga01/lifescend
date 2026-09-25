<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'color',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
