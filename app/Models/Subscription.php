<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'amount',
        'is_end_of_month',
        'billing_day',
        'is_active',
        'archived_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_end_of_month' => 'boolean',
        'billing_day' => 'integer',
        'is_active' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
