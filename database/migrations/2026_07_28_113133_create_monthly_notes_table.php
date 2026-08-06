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
        Schema::create('monthly_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('period_start_date');

            $table->date('period_end_date');

            $table->text('note')->nullable();

            $table->timestamps();

            /*
             * 同じユーザー・同じ予算期間には、
             * ノートを1件だけ登録できるようにする。
             */
            $table->unique(
                [
                    'user_id',
                    'period_start_date',
                    'period_end_date',
                ],
                'monthly_notes_user_period_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_notes');
    }
};