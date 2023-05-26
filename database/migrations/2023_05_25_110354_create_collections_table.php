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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('search_mode');
            $table->foreignId('user_id')->constrained();
            $table->json('search_data')->nullable();
            $table->string('source_filepath')->nullable();
            $table->string('image_path')->nullable();
            $table->json('working_data')->nullable();
            $table->json('report')->nullable();
            $table->timestamps();
            $table->dateTime('consulted_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
