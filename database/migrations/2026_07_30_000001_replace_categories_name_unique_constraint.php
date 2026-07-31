<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'name']);
        });

        DB::statement(
            'CREATE UNIQUE INDEX categories_user_id_name_unarchived_unique
            ON categories (user_id, name)
            WHERE archived_at IS NULL'
        );
    }

    public function down(): void
    {
        DB::statement(
            'DROP INDEX IF EXISTS
            categories_user_id_name_unarchived_unique'
        );

        Schema::table('categories', function (Blueprint $table) {
            $table->unique(['user_id', 'name']);
        });
    }
};
