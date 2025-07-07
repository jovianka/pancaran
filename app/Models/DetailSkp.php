<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailSkp extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'detail_skp';

    protected $fillable = [
        'category',
        'description',
        'role',
        'event_level',
        'skp',
    ];

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'detail_skp_id');
    }

    public function role(): HasMany
    {
        return $this->hasMany(EventRole::class);
    }
}
