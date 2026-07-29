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
        Schema::create('monthly_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('period_start_date');
            $table->date('period_end_date');

            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique([
                'user_id',
                'period_start_date',
                'period_end_date',
            ]);
        });

        DB::statement('
            ALTER TABLE monthly_notes
            ADD CONSTRAINT monthly_notes_period_check
            CHECK (period_end_date >= period_start_date)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_notes');
    }
};