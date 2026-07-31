<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'color_code',
        'sort_order',
        'is_active',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * @var array<int, array{name: string, color_code: string}>
     */
    private const DEFAULT_CATEGORIES = [
        [
            'name' => 'Utilities',
            'color_code' => '#2563EB',
        ],
        [
            'name' => 'Food',
            'color_code' => '#C2410C',
        ],
        [
            'name' => 'Entertainment',
            'color_code' => '#7C3AED',
        ],
        [
            'name' => 'Transport',
            'color_code' => '#047857',
        ],
        [
            'name' => 'Rent',
            'color_code' => '#475569',
        ],
    ];

    /**
     * Create missing default categories for the specified user.
     */
    public static function createDefaultsFor(User $user): void
    {
        foreach (self::DEFAULT_CATEGORIES as $index => $category) {
            self::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $category['name'],
                ],
                [
                    'color_code' => $category['color_code'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budget(): HasOne
    {
        return $this->hasOne(CategoryBudget::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
