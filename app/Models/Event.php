<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'event';

    // public $timestamps = false;

    public function scopeVisibleToUser(Builder $query, $user, array $filters)
    {
        $query->where(fn ($query) =>
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


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withPivot('status')->withTimestamps();
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
}
