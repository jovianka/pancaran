<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EventPermission extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event_permission';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(EventRole::class, 'event_role_permission')->withTimestamps();
    }
}

