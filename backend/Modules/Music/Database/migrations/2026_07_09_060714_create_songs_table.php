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
        Schema::create('songs', function (Blueprint $table) {

            $table->id();
            $table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            $table->foreignId('album_id')->nullable()->constrained('albums')->nullOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->string('title');
            $table->longText('lyrics')->nullable();
            $table->integer('duration')->default(0);
            $table->string('original_file_path')->nullable();
            $table->string('hls_path')->nullable();
            $table->bigInteger('original_size')->default(0);
            $table->integer('bitrate')->default(0);
            $table->integer('sample_rate')->default(0);
            $table->tinyInteger('channels')->default(2);
            $table->string('checksum', 64)->nullable()->unique();
            $table->string('cover_image')->nullable();
            $table->string('status', 50)->default('Draft');
            $table->bigInteger('play_count')->default(0);
            $table->text('rejected_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('processing_error')->nullable();
            $table->tinyInteger('processing_attempts')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('title');
            $table->index('status');
            $table->index(['status', 'artist_id']);
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
