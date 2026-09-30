<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiOpsBrief extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metrics' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function isAiGenerated(): bool
    {
        return $this->status === 'success';
    }
}
