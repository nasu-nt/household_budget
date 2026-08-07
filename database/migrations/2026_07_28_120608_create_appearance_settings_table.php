<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appearance_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('all_good_color', 7)
                ->default('#F8FAFC');

            $table->string('slightly_high_color', 7)
                ->default('#F7E7A6');

            $table->string('over_budget_color', 7)
                ->default('#F3C38C');

            $table->string('over_limit_color', 7)
                ->default('#EE8B8B');

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appearance_settings');
    }
};