<?php

namespace Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;

class AdminAuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::with('user:id,name,email');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by module (auditable_type)
        if ($request->filled('module')) {
            $query->where('auditable_type', 'like', '%' . $request->module . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách nhật ký hệ thống thành công',
            'data' => $logs
        ]);
    }

    /**
     * Get recent activities for the dashboard.
     */
    public function recent(): JsonResponse
    {
        $logs = AuditLog::with('user:id,name,email')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy hoạt động gần đây thành công',
            'data' => $logs
        ]);
    }
}
