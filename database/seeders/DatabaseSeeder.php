<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            [
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ],
        );

        /*
         * 今後、デモ用のカテゴリ・予算・支出データを
         * $demoUserに紐づけて登録する。
         */
        User::query()->updateOrCreate(
            [
                'email' => config(
                    'demo.email',
                    'demo@example.com',
                ),
            ],
            [
                'name' => 'Demo User',
                'password' => Hash::make('demo-password'),
            ],
        );

        /*
         * ユーザーの作成後に、ユーザーに紐づく
         * 初期データを登録する。
         */
        $this->call([
            CategorySeeder::class,
            AppearanceSettingSeeder::class,
            BudgetSettingSeeder::class,
        ]);
    }
}