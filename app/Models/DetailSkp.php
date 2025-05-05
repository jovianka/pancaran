<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailSkp extends Model
{
    protected $guarded = ['id'];
    protected $table = 'detail_skp';

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function role(): HasMany
    {
        return $this->hasMany(EventRole::class);
    }
}
