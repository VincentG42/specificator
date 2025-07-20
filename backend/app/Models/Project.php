<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'progress',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'progress' => 'integer'
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
