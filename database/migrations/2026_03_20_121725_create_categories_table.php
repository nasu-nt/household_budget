<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * まずcategoriesテーブルを作成する。
         */
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 50);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'user_id',
                'name',
            ]);
        });

        /*
         * テーブル作成が完了してから、
         * PostgreSQLのCHECK制約を追加する。
         */
        DB::statement(
            <<<'SQL'
                ALTER TABLE categories
                ADD CONSTRAINT categories_sort_order_non_negative_check
                CHECK (sort_order >= 0)
            SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};