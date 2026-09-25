<?php

namespace App\Models;

/**
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> organizations(string ...$params)
 * @method \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> orders(string ...$params)
 * @method \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> tickets(string ...$params)
 * @method \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> registrations(string ...$params)
 * @method \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingRequest> trainingRequests(string ...$params)
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> savedEvents(string ...$params)
 * @method \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Collection<int, \App\Models\EventEnquiry> enquiries(string ...$params)
 */
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

#[Fillable(['name', 'username', 'email', 'password', 'avatar', 'phone', 'timezone', 'status', 'accepted_terms_version', 'accepted_privacy_version'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_until' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function trainingRequests()
    {
        return $this->hasMany(TrainingRequest::class);
    }

    public function savedEvents()
    {
        return $this->belongsToMany(Event::class, 'saved_events')->withTimestamps();
    }

    public function enquiries()
    {
        return $this->hasMany(EventEnquiry::class, 'contact', 'email');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
