<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRegistration extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event_registration';

    public function scopeVisibleToUser(Builder $query, $user, array $filters)
    {
        $query->where('status', 'open')->whereHas( 'event',fn ($query) =>
            $query->whereIn('event_level', ['university', 'international', 'regional', 'national'])

                ->orWhere(fn ($query) =>
                    $query->where('event_level', 'faculty')//Cek apabila event levelnya faculty/facultas
                        ->where('faculty_id', $user->faculty_id)
                )

                ->orWhere(fn ($query) =>
                    $query->where('event_level', 'major') //Cek apabila event levelnya major/prodi
                        ->where('major_id', $user->major_id)
                )
        );

        $query->when($filters['search']??false, fn ($query, $filters) => $query->where('name', 'like', '%'. request('search').'%'));


        return $query;

    }

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
