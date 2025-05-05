<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratTugas extends Model
{
    protected $guarded = ['id'];
    protected $table = 'surat_tugas';

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
