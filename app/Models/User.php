<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nim',
        'name',
        'email',
        'password',
        'avatar',
        'type',
        'faculty_id',
        'major_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function eventRegistrationResponses(): HasMany
    {
        return $this->hasMany(EventRegistrationResponse::class);
    }
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'recipient_id');
    }
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')->withPivot('status')->withTimestamps();
    }
    public function eventUsers(): HasMany
    {
        return $this->hasMany(EventUser::class);
    }

}
