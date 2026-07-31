<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budget_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('monthly_budget');
            $table->integer('monthly_limit');
            $table->boolean('is_end_of_month')->default(true);
            $table->smallInteger('closing_day')->nullable();
            $table->timestamps();
        });

        DB::statement(
            <<<'SQL'
                ALTER TABLE budget_settings
                    ADD CONSTRAINT budget_settings_monthly_budget_positive
                        CHECK (monthly_budget > 0),
                    ADD CONSTRAINT budget_settings_limit_gte_budget
                        CHECK (monthly_limit >= monthly_budget),
                    ADD CONSTRAINT budget_settings_closing_day_consistent
                        CHECK (
                            (is_end_of_month = TRUE AND closing_day IS NULL)
                            OR
                            (
                                is_end_of_month = FALSE
                                AND closing_day BETWEEN 1 AND 31
                            )
                        )
                SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_settings');
    }
};