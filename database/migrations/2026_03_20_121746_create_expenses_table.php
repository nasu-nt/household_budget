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
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedInteger('amount');
            $table->date('expense_date');
            $table->string('memo', 255)->nullable();

            $table->timestamps();

            // ユーザーごとの日付検索を速くする
            $table->index(['user_id', 'expense_date']);

            // カテゴリーごとの日付検索を速くする
            $table->index(['category_id', 'expense_date']);
        });
        
        DB::statement('
            ALTER TABLE expenses
            ADD CONSTRAINT expenses_amount_positive_check
            CHECK (amount >= 0)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
