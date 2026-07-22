<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class Setting
{
    /**
     * Lấy giá trị setting theo key, trả về $default nếu không tìm thấy.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('system_settings', function () {
            return SystemSetting::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Lấy tất cả settings, có thể lọc theo group.
     */
    public static function all(?string $group = null): array
    {
        $query = SystemSetting::query();

        if ($group) {
            $query->where('group', $group);
        }

        return $query->get()->keyBy('key')->toArray();
    }

    /**
     * Xoá cache sau khi cập nhật setting.
     */
    public static function clearCache(): void
    {
        Cache::forget('system_settings');
    }
}
