<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'event_level',
        'poster',
        'start_date',
        'end_date',
        'job_description',
        'requirements',
        'status',
        'parent_id',
        'faculty_id',
        'major_id',
    ];

    protected $table = 'event';

    use HasFactory;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withPivot(['status', 'event_role_id'])->withTimestamps();
    }

    public function eventUsers(): HasMany
    {
        return $this->hasMany(EventUser::class);
    }

    /**
     * Event members whose membership is currently active.
     */
    public function activeEventUsers(): HasMany
    {
        return $this->hasMany(EventUser::class)->where('status', 'active');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(EventRole::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function suratTugas(): HasOne
    {
        return $this->hasOne(SuratTugas::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_tag')->withTimestamps();
    }

    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'parent_id');
    }

    public function childEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'parent_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ContactPerson::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Select events where current the user belongs to.
     */
    #[Scope]
    protected function userActivities($query = null): Builder
    {
        return $query->whereHas('users', function (Builder $userQuery) {
            $userQuery->where('user_id', '=', Auth::id())
                ->whereNot('status', '=', 'removed');
        });
    }

    /**
     * Select events where current the user belongs to.
     */
    #[Scope]
    protected function ongoingUserActivities($query = null): Builder
    {
        return $query->userActivities()->where('status', '=', 'ongoing');
    }
}
