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
        Schema::create('follows', function (Blueprint $table) {

            $table->id();
            $table->foreignId('listener_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['listener_id', 'artist_id']);
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
