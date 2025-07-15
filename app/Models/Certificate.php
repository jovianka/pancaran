<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'certificate';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailSkp(): BelongsTo
    {
        return $this->belongsTo(DetailSkp::class, 'detail_skp_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(EventRole::class, 'event_role_id');
    }
}
