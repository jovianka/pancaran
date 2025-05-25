<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRole extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event_role';

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(EventPermission::class, 'event_role_permission')->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withPivot('status');
    }

    public function eventRegistrations(): BelongsToMany
    {
        return $this->belongsToMany(EventRegistration::class, 'event_registration_role');
    }

    public function detailSkp(): BelongsTo
    {
        return $this->belongsTo(DetailSkp::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
