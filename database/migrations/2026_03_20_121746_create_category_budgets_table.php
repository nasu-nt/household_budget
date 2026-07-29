<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_budgets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('target_amount');
            $table->unsignedInteger('limit_amount');

            $table->timestamps();

            $table->unique('category_id');
        });

        DB::statement('
            ALTER TABLE category_budgets
            ADD CONSTRAINT category_budgets_target_amount_non_negative_check
            CHECK (target_amount >= 0)
        ');

        DB::statement('
            ALTER TABLE category_budgets
            ADD CONSTRAINT category_budgets_limit_amount_non_negative_check
            CHECK (limit_amount >= 0)
        ');

        DB::statement('
            ALTER TABLE category_budgets
            ADD CONSTRAINT category_budgets_limit_amount_check
            CHECK (limit_amount >= target_amount)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_budgets');
    }
};
