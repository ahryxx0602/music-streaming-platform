<?php

namespace Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Administration\Models\Setting;
use Modules\Administration\Models\AuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AdminSettingController extends Controller
{
    /**
     * Get all system settings as key-value pairs
     */
    public function index(): JsonResponse
    {
        // Cache settings forever to optimize performance
        $settings = Cache::rememberForever('system_settings', function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        return response()->json([
            'message' => 'Lấy cấu hình hệ thống thành công',
            'data' => $settings
        ]);
    }

    /**
     * Bulk update system settings
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        $newSettings = $request->input('settings');
        $oldSettings = Cache::get('system_settings', []);

        foreach ($newSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value]
            );
        }

        // Clear cache
        Cache::forget('system_settings');
        
        // Fetch fresh settings for Audit Log
        $freshSettings = Setting::pluck('value', 'key')->toArray();

        // Log the bulk update action manually since we updated multiple rows
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'entity_type' => Setting::class,
            'entity_id' => 0, // 0 represents bulk system settings
            'old_values' => json_encode($oldSettings),
            'new_values' => json_encode($freshSettings),
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'message' => 'Cập nhật cấu hình hệ thống thành công',
            'data' => $freshSettings
        ]);
    }
}
