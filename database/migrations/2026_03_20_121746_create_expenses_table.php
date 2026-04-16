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

            // 下記1→2の検索を速くするためのインデックス
            $table->index(['user_id', 'expense_date']); // 1.user_idで絞る
            $table->index(['category_id', 'expense_date']); // 2.そのあとexpense_dateで絞る
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
