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
        Schema::create('tg_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id');
            $table->text('text')->nullable();
            $table->text('log')->nullable();
            $table->boolean('act')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tg_logs');
    }
};
