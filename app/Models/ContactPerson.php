<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactPerson extends Model
{
    /** @use HasFactory<\Database\Factories\ContactPersonFactory> */
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'contact_person';

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
