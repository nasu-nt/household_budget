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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name', 100);
            $table->unsignedInteger('amount');

            $table->unsignedTinyInteger('billing_day')->nullable();
            $table->boolean('is_end_of_month')->default(true);

            $table->boolean('is_active')->default(true);
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'archived_at',
            ]);
        });

        DB::statement('
            ALTER TABLE subscriptions
            ADD CONSTRAINT subscriptions_amount_positive_check
            CHECK (amount > 0)
        ');

        DB::statement('
            ALTER TABLE subscriptions
            ADD CONSTRAINT subscriptions_billing_day_range_check
            CHECK (
                billing_day IS NULL
                OR (billing_day >= 1 AND billing_day <= 31)
            )
        ');

        DB::statement('
            ALTER TABLE subscriptions
            ADD CONSTRAINT subscriptions_billing_day_end_of_month_check
            CHECK (
                (is_end_of_month = true AND billing_day IS NULL)
                OR
                (is_end_of_month = false AND billing_day IS NOT NULL)
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};