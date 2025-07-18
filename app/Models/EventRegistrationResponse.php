<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistrationResponse extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event_registration_response';

    protected $fillable = [
        'date_submitted',
        'user_id',
        'event_registration_id',
        'details',
    ];
    protected $casts = [
        'details' => 'array',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventRegistration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class);
    }

}
