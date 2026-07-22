<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'MusicStream', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Nơi âm nhạc không ngừng nghỉ', 'type' => 'string', 'group' => 'general'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general'],

            // Upload Limits
            ['key' => 'max_upload_size', 'value' => '52428800', 'type' => 'integer', 'group' => 'uploads'], // 50MB in bytes
            ['key' => 'allowed_audio_formats', 'value' => 'mp3,wav,flac', 'type' => 'string', 'group' => 'uploads'],

            // Financial
            ['key' => 'artist_revenue_share', 'value' => '70', 'type' => 'integer', 'group' => 'financial'], // 70%
            ['key' => 'platform_commission', 'value' => '30', 'type' => 'integer', 'group' => 'financial'], // 30%
            ['key' => 'minimum_payout', 'value' => '500000', 'type' => 'integer', 'group' => 'financial'], // VND

            // Content
            ['key' => 'allow_explicit_content', 'value' => 'false', 'type' => 'boolean', 'group' => 'content'],
            ['key' => 'song_pending_review_days', 'value' => '3', 'type' => 'integer', 'group' => 'content'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
