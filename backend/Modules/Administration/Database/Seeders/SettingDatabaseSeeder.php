<?php

namespace Modules\Administration\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Administration\Models\Setting;

class SettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            ['key' => 'site_name', 'value' => 'Harmonia', 'description' => 'Tên nền tảng'],
            ['key' => 'support_email', 'value' => 'support@harmonia.com', 'description' => 'Email chăm sóc khách hàng'],
            ['key' => 'artist_revenue_share', 'value' => '70', 'description' => 'Tỉ lệ chia sẻ doanh thu cho nghệ sĩ (%)'],
            ['key' => 'payout_threshold', 'value' => '50', 'description' => 'Ngưỡng rút tiền tối thiểu ($)'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'description' => 'Chế độ bảo trì hệ thống'],
            ['key' => 'max_upload_size', 'value' => '20', 'description' => 'Dung lượng upload tối đa (MB)'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'description' => $setting['description']]
            );
        }
    }
}
