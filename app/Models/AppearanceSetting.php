<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppearanceSetting extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    public const DEFAULT_COLORS = [
        'all_good_color' => '#F8FAFC',
        'slightly_high_color' => '#F7E7A6',
        'over_budget_color' => '#F3C38C',
        'over_limit_color' => '#EE8B8B',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'all_good_color',
        'slightly_high_color',
        'over_budget_color',
        'over_limit_color',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}