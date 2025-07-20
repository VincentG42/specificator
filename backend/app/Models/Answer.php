<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'project_id',
        'section_key',
        'question_key',
        'answer_value',
        'is_not_applicable'
    ];
    
    protected $casts = [
        'answer_value' => 'array',
        'is_not_applicable' => 'boolean'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
