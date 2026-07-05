<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
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

    /**
     * Per-request cache of the user's active membership per event id.
     *
     * @var array<int, EventUser|null>
     */
    protected array $eventMembershipCache = [];

    /**
     * Resolve (and cache) the user's active membership for the given event,
     * eager-loading the role and its permissions to avoid N+1 lookups.
     */
    public function activeMembership(Event $event): ?EventUser
    {
        if (! array_key_exists($event->id, $this->eventMembershipCache)) {
            $this->eventMembershipCache[$event->id] = $this->eventUsers()
                ->where('event_id', $event->id)
                ->where('status', 'active')
                ->with('role.permissions')
                ->first();
        }

        return $this->eventMembershipCache[$event->id];
    }

    /**
     * The user's active role in an event, or null if not an active member.
     */
    public function roleInEvent(Event $event): ?EventRole
    {
        return $this->activeMembership($event)?->role;
    }

    /**
     * Whether the user holds the case-insensitive "Admin" role in the event.
     */
    public function isEventAdmin(Event $event): bool
    {
        return strtolower((string) $this->roleInEvent($event)?->name) === 'admin';
    }

    /**
     * Whether the user may perform an action gated by the given permission in
     * the event. The event Admin implicitly holds every permission.
     */
    public function hasEventPermission(Event $event, string $permission): bool
    {
        if ($this->isEventAdmin($event)) {
            return true;
        }

        $role = $this->roleInEvent($event);

        return $role !== null && $role->permissions->contains('name', $permission);
    }
}
