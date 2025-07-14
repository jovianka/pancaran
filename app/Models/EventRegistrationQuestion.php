<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistrationQuestion extends Model
{
    protected $guarded = ['id'];
    protected $table = 'event_registration_question';

    protected $fillable = [
        'title',
        'description',
        'questions',
        'type',
        'event_registration_id'
    ];

    protected $casts = [
        'questions' => 'array'
    ];

    public function scopeGetQuestions(Builder $query, $registration_id){
        return $query->where('event_registration_id', $registration_id)->latest()->first();
    }
    public function eventRegistration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class);
    }
}
