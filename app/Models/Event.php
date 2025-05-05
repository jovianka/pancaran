<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event';

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withPivot('status');
    }

    public function eventUsers(): HasMany
    {
        return $this->hasMany(EventUser::class);
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
}
