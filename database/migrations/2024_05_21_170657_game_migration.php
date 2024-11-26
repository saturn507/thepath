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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_line_id')
                ->unsigned()
                ->nullable()
                ->constrained('quest_lines')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->boolean('act')->nullable(false)->default(true);
            $table->timestamp('start_at')->nullable();
            $table->string('team_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
