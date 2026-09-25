<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingTopic extends Model
{
    protected $fillable = ['event_id', 'name', 'description', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
