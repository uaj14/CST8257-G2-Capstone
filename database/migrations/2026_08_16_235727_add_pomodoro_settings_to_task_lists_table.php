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
        Schema::table('task_lists', function (Blueprint $table) {
            $table->boolean('pomodoro_enabled')->default(false)->after('color');
            $table->unsignedSmallInteger('pomodoro_minutes')->default(25)->after('pomodoro_enabled');
            $table->unsignedSmallInteger('break_minutes')->default(5)->after('pomodoro_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_lists', function (Blueprint $table) {
            $table->dropColumn(['pomodoro_enabled', 'pomodoro_minutes', 'break_minutes']);
        });
    }
};
