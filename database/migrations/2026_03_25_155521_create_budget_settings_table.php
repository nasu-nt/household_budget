<?php

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
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('monthly_target_amount');
            $table->unsignedInteger('monthly_limit_amount');

            $table->unsignedTinyInteger('closing_day')->nullable();
            $table->boolean('is_end_of_month')->default(false);

            $table->timestamps();

            $table->unique('user_id');
        });

        DB::statement('
            ALTER TABLE budget_settings
            ADD CONSTRAINT budget_settings_limit_amount_check
            CHECK (monthly_limit_amount >= monthly_target_amount)
        ');

        DB::statement('
            ALTER TABLE budget_settings
            ADD CONSTRAINT budget_settings_closing_day_range_check
            CHECK (
                closing_day IS NULL
                OR (closing_day >= 1 AND closing_day <= 31)
            )
        ');

        DB::statement('
            ALTER TABLE budget_settings
            ADD CONSTRAINT budget_settings_closing_day_end_of_month_check
            CHECK (
                (is_end_of_month = true AND closing_day IS NULL)
                OR
                (is_end_of_month = false AND closing_day IS NOT NULL)
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_settings');
    }
};
