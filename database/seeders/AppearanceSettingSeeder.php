<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AppearanceSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppearanceSettingSeeder extends Seeder
{
    /**
     * Seed appearance settings for existing users.
     */
    public function run(): void
    {
        User::query()
            ->select('id')
            ->eachById(function (User $user): void {
                AppearanceSetting::query()->firstOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    AppearanceSetting::DEFAULT_COLORS,
                );
            });
    }
}