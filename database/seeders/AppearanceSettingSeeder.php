<?php

namespace Database\Seeders;

use App\Models\AppearanceSetting;
use App\Models\User;
use Illuminate\Database\Seeder;


class AppearanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()
            ->select('id')
            ->eachById(function (User $user): void {
                AppearanceSetting::query()->firstOrCreate(
                    ['user_id' => $user->id],
                    AppearanceSetting::DEFAULT_COLORS,
                );
            });

        $this->call([
                AppearanceSettingSeeder::class,
            ]);
        }
}