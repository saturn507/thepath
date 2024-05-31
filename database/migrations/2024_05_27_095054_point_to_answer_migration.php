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
        Schema::create('point_to_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('point_id')
                ->constrained('points')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('answer')->nullable(false);
            $table->string('answer_transformation')->nullable(false);

            $table->index('point_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_to_answers');
    }
};
