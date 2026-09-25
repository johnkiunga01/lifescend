<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function fields()
    {
        return $this->hasMany(RegistrationFormField::class, 'form_id')->orderBy('order');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'form_id');
    }
}
