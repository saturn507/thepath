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
        Schema::create('game_to_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')
                ->constrained('games')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('point_id')
                ->constrained('points')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('completed')->nullable(false)->default(false);
            $table->string('answer')->nullable();

            $table->timestamps();

            $table->index('game_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_to_points');
    }
};
