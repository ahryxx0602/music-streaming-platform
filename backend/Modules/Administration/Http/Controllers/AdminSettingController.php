<?php

namespace Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use App\Helpers\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AdminSettingController extends Controller
{
    /**
     * GET: Lấy toàn bộ System Settings (cache-first)
     */
    public function index(): JsonResponse
    {
        $settings = Cache::rememberForever('system_settings', function () {
            return SystemSetting::all()->groupBy('group')->map(function ($group) {
                return $group->keyBy('key')->map(function ($item) {
                    return [
                        'value' => $item->value,
                        'type' => $item->type,
                    ];
                });
            })->toArray();
        });

        return response()->json([
            'success' => true,
            'message' => 'Lấy cấu hình hệ thống thành công',
            'data' => $settings
        ]);
    }

    /**
     * PUT: Bulk update System Settings
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $incomingSettings = $request->input('settings');

        // Lấy giá trị cũ từ DB để ghi audit
        $oldValues = SystemSetting::whereIn('key', array_keys($incomingSettings))
            ->pluck('value', 'key')
            ->toArray();

        foreach ($incomingSettings as $key => $value) {
            // Cast boolean/integer values to string for storage
            $storedValue = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;

            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $storedValue]
            );
        }

        // BẮT BUỘC clear cache sau khi update
        Setting::clearCache();

        // Ghi audit log bulk update thủ công
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'auditable_type' => SystemSetting::class,
            'auditable_id' => 0, // 0 đại diện cho bulk update
            'old_values' => $oldValues ?: null,
            'new_values' => $incomingSettings,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Lấy dữ liệu fresh để trả về
        $fresh = SystemSetting::pluck('value', 'key')->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật cấu hình hệ thống thành công',
            'data' => $fresh
        ]);
    }
}
