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
        Schema::create('streams', function (Blueprint $table) {

            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            $table->string('device')->nullable();
            $table->binary('ip_address')->nullable();
            $table->string('session_id')->nullable();
            $table->integer('duration')->default(0);
            $table->timestamp('streamed_at')->useCurrent()->index();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streams');
    }
};
