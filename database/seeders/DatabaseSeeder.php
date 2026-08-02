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
         * 既存Seederがデモユーザーを参照していても動くように、
         * 先にユーザーだけ作成しておく。
         */
        User::query()->updateOrCreate(
            [
                'email' => config('demo.email', 'demo@example.com'),
            ],
            [
                'name' => config('demo.name', 'Demo User'),
                'password' => Hash::make(
                    config('demo.password', 'demo-password'),
                ),
            ],
        );

        $this->call([
            CategorySeeder::class,
            BudgetSettingSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}