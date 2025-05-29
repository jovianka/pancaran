<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRegistration extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event_registration';

    public function questions(): HasMany
    {
        return $this->hasMany(EventRegistrationQuestion::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(EventRegistrationResponse::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(EventRole::class, 'event_registration_role');
    }
}
