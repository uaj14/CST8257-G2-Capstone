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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId("task_list_id")
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string("name");
            $table->integer("priority");
            $table->integer("position")->nullable();
            $table->unique([        // This ensures a given task's position in a given task_list.
                'task_list_id',
                'position'
            ]);
            $table->text("description")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
