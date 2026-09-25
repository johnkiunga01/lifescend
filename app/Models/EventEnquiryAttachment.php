<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEnquiryAttachment extends Model
{
    protected $fillable = [
        'event_enquiry_id',
        'original_name',
        'path',
        'mime',
        'size',
    ];

    public function enquiry()
    {
        return $this->belongsTo(EventEnquiry::class, 'event_enquiry_id');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime === 'application/pdf';
    }

    public function isPreviewable(): bool
    {
        return $this->isImage() || $this->isPdf();
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $bytes < 10 && $i > 0 ? 1 : 0) . ' ' . $units[$i];
    }
}
