<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'favicon',
        'branding_primary_color',
        'branding_secondary_color',
        'website',
        'email',
        'phone',
        'address',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * The organization's default timezone, used as the fallback wherever the
     * system needs a timezone but a more specific one (event/user) isn't set.
     * Stored in `settings` rather than its own column since it's just another
     * piece of organization configuration.
     */
    public function getTimezoneAttribute(): string
    {
        return $this->settings['timezone'] ?? config('app.timezone');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events()
    {
        // Notice: Events are scoped by created_by or organization context
        // In our migration, events table doesn't have organization_id directly.
        // It has created_by (FK to users) and venue_id.
        // To relate organization to events, we can find events created by the users of this organization.
        // Or, if organizations have events, wait, let's verify if organizations and events are related.
        // The ERD Summary says:
        // Organizations -> Events
        // Events table in the spec:
        // events:
        // - id
        // - name
        // - slug
        // - description
        // - start_date
        // - end_date
        // - timezone
        // - venue_id
        // - event_type
        // - capacity
        // - logo
        // - cover_image
        // - theme_primary
        // - theme_secondary
        // - status (draft/published/archived)
        // - published_at
        // - created_by
        // - timestamps
        // Ah! Events table only has created_by. So we can relate events through users, or just events created by the owner of the organization. Let's write a custom relationship or direct query. If the user who created the event is part of the organization, it's scoped.
        return $this->hasMany(Event::class, 'created_by', 'created_by');
    }
}
