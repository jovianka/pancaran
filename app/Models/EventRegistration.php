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

    public function scopeVisibleToUser(Builder $query, $user, $by, $tags, $scopes, $title)
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

        // if (!empty($by)) {
        //     $query->whereIn('author_username', $by);
        // }

        // if (!empty($tags)) {
        //     $query->whereHas('tags', function ($q) use ($tags) {
        //         $q->whereIn('name', $tags);
        //     });
        // }

        // if (!empty($scopes)) {
        //     $query->whereIn('scope', $scopes); // jika ada kolom scope
        // }

        // if (!empty($title)) {
        //     $query->where('title', 'like', '%' . $title . '%');
        // }

        $query->when($by ?? false, fn ($query) =>
            $query->whereHas('event.eventUsers', fn($query) =>
                $query -> whereHas('role', fn($query)=>
                    $query -> where('name', 'admin')
                )->whereHas('user', fn($query) =>
                    $query->whereIn('name', $by)
                    )
            )
        );

        $query->when($tags ?? false, fn ($query) =>
            $query->whereHas('event', fn($query) =>
                $query -> whereHas('tags', fn($query)=>
                    $query -> whereIn('name', $tags)
                )
            )
        );

        $query->when($scopes ?? false, fn ($query) =>
            $query->whereHas('event', fn($query) =>
                $query -> whereIn('even_level', $scopes)
            )
        );

        $query->when($title ?? false, function($query) use ($title) {
            $query->whereHas('event', function($query) use ($title) {
                $query->where(function($query) use ($title) {
                    collect($title)->each(function($term) use ($query) {
                        $query->orWhere('name', 'ilike', '%' . $term . '%');
                    });
                });
            });
        });


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
