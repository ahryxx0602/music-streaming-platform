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
        Schema::create('song_artists', function (Blueprint $table) {

            $table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            $table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            $table->string('role')->default('featured');
            $table->primary(['song_id', 'artist_id']);
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_artists');
    }
};
