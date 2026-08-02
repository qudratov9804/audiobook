<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('transcripts');
    }

    public function down(): void
    {
        Schema::create('transcripts', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('audio_chunk_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_final')->default(false);
            $table->longText('content')->nullable();
            $table->string('format', 10)->default('txt');
            $table->string('disk')->default('transcripts');
            $table->string('path')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index(['book_id', 'is_final']);
        });
    }
};
