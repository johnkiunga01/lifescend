<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'field_id',
        'value',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function field()
    {
        return $this->belongsTo(RegistrationFormField::class, 'field_id');
    }
}
