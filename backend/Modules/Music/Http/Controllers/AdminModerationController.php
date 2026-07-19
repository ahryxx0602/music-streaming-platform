<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Music\Models\Song;

class AdminModerationController extends Controller
{
    /**
     * [API-ADM-xx] Lấy danh sách bài hát chờ duyệt
     */
    public function index(Request $request): JsonResponse
    {
        $query = Song::where('status', 'Pending')
                     ->where('processing_status', 'completed')
                     ->with(['artist', 'genre']);

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $songs = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $songs
        ]);
    }

    /**
     * [API-ADM-xx] Lấy chi tiết bài hát
     */
    public function show($id): JsonResponse
    {
        $song = Song::with(['artist', 'genre'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'song' => $song
            ]
        ]);
    }

    /**
     * [API-ADM-xx] Chấp nhận bài hát
     */
    public function approve($id): JsonResponse
    {
        $song = Song::findOrFail($id);

        if ($song->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể duyệt bài hát ở trạng thái Pending.'
            ], 400);
        }

        $song->update([
            'status' => 'Approved',
            'approved_at' => now(),
            'rejected_reason' => null,
            'rejected_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt bài hát thành công.',
            'data' => [
                'song' => $song
            ]
        ]);
    }

    /**
     * [API-ADM-xx] Từ chối bài hát
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $request->validate([
            'reject_reason' => 'required|string|min:10'
        ]);

        $song = Song::findOrFail($id);

        if ($song->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể từ chối bài hát ở trạng thái Pending.'
            ], 400);
        }

        $song->update([
            'status' => 'Rejected',
            'rejected_reason' => $request->reject_reason,
            'rejected_at' => now(),
            'approved_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối bài hát.',
            'data' => [
                'song' => $song
            ]
        ]);
    }
}
