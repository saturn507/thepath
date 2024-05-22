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
        Schema::create('quest_line_to_points', function (Blueprint $table) {
            $table->foreignId('quest_line_id')
                ->constrained('quest_lines')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('point_id')
                ->constrained('points')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_line_to_points');
    }
};
