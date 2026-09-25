<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'label',
        'type',
        'options',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(RegistrationForm::class, 'form_id');
    }

    public function values()
    {
        return $this->hasMany(RegistrationFieldValue::class, 'field_id');
    }
}
