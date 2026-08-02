<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('audio_files', 'book_sections');

        Schema::table('book_sections', function (Blueprint $table) {
            $table->string('name')->nullable()->after('book_id');
            $table->text('description')->nullable()->after('name');
        });

        DB::table('book_sections')->whereNull('name')->update(['name' => 'Bo\'lim 1']);

        Schema::table('book_sections', function (Blueprint $table) {
            $table->dropIndex('audio_files_status_index');
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('book_sections', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->index('status');
        });

        Schema::table('book_sections', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::rename('book_sections', 'audio_files');
    }
};
