<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'start_date',
        'end_date',
        'timezone',
        'venue_id',
        'event_type',
        'payment_type',
        'capacity',
        'logo',
        'cover_image',
        'theme_primary',
        'theme_secondary',
        'status',
        'published_at',
        'created_by',
        'meeting_place',
        'platform',
        'meeting_link',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'capacity' => 'integer',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function trainingRequests()
    {
        return $this->hasMany(TrainingRequest::class);
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'speaker_event')
            ->withPivot('featured', 'speak_time')
            ->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function registrationForms()
    {
        return $this->hasMany(RegistrationForm::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function topics()
    {
        return $this->hasMany(TrainingTopic::class)->orderBy('sort_order');
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? (str_starts_with($this->logo, 'http') ? $this->logo : asset('storage/' . $this->logo)) : null;
    }

    public function getCoverUrlAttribute()
    {
        return $this->cover_image ? (str_starts_with($this->cover_image, 'http') ? $this->cover_image : asset('storage/' . $this->cover_image)) : asset('images/events_placeholder.png');
    }

    public function getDescriptionHtmlAttribute()
    {
        if (!$this->description) {
            return null;
        }

        // Descriptions saved before the rich text editor was added are plain
        // text with raw newlines; convert them to paragraphs/line breaks so
        // they render correctly. Content already containing HTML (from the
        // editor) is passed through as-is.
        if (strip_tags($this->description) !== $this->description) {
            return $this->renderDescriptionShortcodes($this->description);
        }

        $paragraphs = preg_split('/\n\s*\n/', trim($this->description));
        $html = implode('', array_map(
            fn ($p) => '<p>' . nl2br(e(trim($p))) . '</p>',
            array_filter($paragraphs, fn ($p) => trim($p) !== '')
        ));

        return $this->renderDescriptionShortcodes($html);
    }

    /**
     * Replace shortcodes embedded in the event description with rendered HTML.
     *
     * Supported: [speakers] (all event speakers) and [speakers ids="1,2"].
     */
    public function renderDescriptionShortcodes(?string $html): ?string
    {
        if (! $html || ! str_contains($html, '[speakers')) {
            return $html;
        }

        // If a shortcode sits alone inside its own paragraph, unwrap it so the
        // rendered markup is not nested inside an invalid <p> element.
        $html = preg_replace('/<p>\s*(\[speakers[^\]]*\])\s*<\/p>/i', '$1', $html);

        return preg_replace_callback(
            '/\[speakers(?:\s+ids?\s*=\s*["\']?([0-9,\s]+)["\']?)?\s*\]/i',
            function (array $matches) {
                $ids = array_values(array_filter(array_map(
                    'intval',
                    preg_split('/[\s,]+/', trim($matches[1] ?? '')) ?: []
                )));

                if ($ids) {
                    $speakers = Speaker::whereIn('id', $ids)->get()
                        ->sortBy(fn ($s) => array_search($s->id, $ids))
                        ->values();
                } else {
                    $speakers = $this->speakers;
                }

                return view('events._speakers', ['speakers' => $speakers])->render();
            },
            $html
        );
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_events')->withTimestamps();
    }
}
