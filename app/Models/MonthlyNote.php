<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyNote extends Model
{
    protected $fillable = [
        'period_start_date',
        'period_end_date',
        'note',
    ];

    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}