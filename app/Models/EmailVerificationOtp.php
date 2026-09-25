<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationOtp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return $this->expires_at->isFuture() && $this->used_at === null;
    }
}
