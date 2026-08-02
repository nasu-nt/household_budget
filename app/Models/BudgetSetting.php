<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetSetting extends Model
{
    use HasFactory;

    /**
     * @var array{
     *     monthly_budget: int,
     *     monthly_limit: int,
     *     is_end_of_month: bool,
     *     closing_day: null
     * }
     */
    public const DEFAULT_VALUES = [
        'monthly_budget' => 128_400,
        'monthly_limit' => 150_000,
        'is_end_of_month' => true,
        'closing_day' => null,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'monthly_budget',
        'monthly_limit',
        'is_end_of_month',
        'closing_day',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_budget' => 'integer',
            'monthly_limit' => 'integer',
            'is_end_of_month' => 'boolean',
            'closing_day' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
