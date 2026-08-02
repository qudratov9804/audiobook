<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('audio_chunks');
    }

    public function down(): void
    {
        Schema::create('audio_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audio_file_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chunk_index');
            $table->string('disk')->default('chunks');
            $table->string('path');
            $table->unsignedInteger('start_time')->default(0);
            $table->unsignedInteger('end_time')->default(0);
            $table->unsignedInteger('duration')->default(0);
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['audio_file_id', 'chunk_index']);
            $table->index('status');
        });
    }
};
