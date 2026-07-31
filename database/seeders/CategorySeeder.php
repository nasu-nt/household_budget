<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories used by the demo account.
     */
    public function run(): void
    {
        $demoUser = User::query()
            ->where(
                'email',
                config('demo.email', 'demo@example.com'),
            )
            ->first();

        if ($demoUser === null) {
            throw new RuntimeException(
                'Create the demo user before running CategorySeeder.',
            );
        }

        $categories = [
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
                'color_code' => '#BE123C',
            ],
        ];

        foreach ($categories as $index => $category) {
            Category::query()->updateOrCreate(
                [
                    'user_id' => $demoUser->id,
                    'name' => $category['name'],
                ],
                [
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'color_code' => $category['color_code'],
                    'archived_at' => null,
                ],
            );
        }
    }
}