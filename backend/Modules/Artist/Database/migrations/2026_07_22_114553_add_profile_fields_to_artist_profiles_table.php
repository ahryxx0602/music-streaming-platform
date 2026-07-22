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
        Schema::table('artist_profiles', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('bio');
            $table->string('avatar_url')->nullable()->after('contact_email');
            $table->string('banner_url')->nullable()->after('avatar_url');
            $table->json('social_links')->nullable()->after('banner_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artist_profiles', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'avatar_url', 'banner_url', 'social_links']);
        });
    }
};
