<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the default categories for all existing users.
     */
    public function run(): void
    {
        User::query()
            ->select('id')
            ->eachById(function (User $user): void {
                Category::createDefaultsFor($user);
            });
    }
}