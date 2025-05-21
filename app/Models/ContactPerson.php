<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactPerson extends Model
{
    protected $guarded = ['id'];
    protected $table = 'contact_person';

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
