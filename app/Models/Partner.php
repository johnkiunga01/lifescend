<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'story',
        'website',
        'logo',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        $src = $this->image ?? $this->logo;
        if (!$src) {
            return null;
        }
        return str_starts_with($src, 'http') ? $src : asset('storage/' . $src);
    }
}