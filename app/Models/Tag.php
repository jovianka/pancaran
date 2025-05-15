<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'tag';

    public $timestamps = false;

    public function event(): HasMany
    {
        return $this->hasMany(Event::class);
    }

}
