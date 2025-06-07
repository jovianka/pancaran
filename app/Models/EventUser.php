<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventUser extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event_user';

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event():BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(EventRole::class, 'event_role_id');
    }
}
