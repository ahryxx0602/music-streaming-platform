<?php

namespace Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Administration\Http\Requests\StoreArtistInviteRequest;
use Modules\Artist\Models\ArtistInvitation;

class AdminArtistInviteController extends Controller
{
    /**
     * [API-ADM-30] Lấy danh sách mã mời
     */
    public function index(Request $request): JsonResponse
    {
        $query = ArtistInvitation::with('createdBy:id,name,email');

        // Optional filtering can be added here if needed

        $invites = $query->latest()->paginate(15);

        // Accessor 'status' is automatically appended via $appends in model.

        return response()->json([
            'success' => true,
            'data' => $invites
        ]);
    }

    /**
     * [API-ADM-31] Tạo mới mã mời
     */
    public function store(StoreArtistInviteRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $expiresInDays = $validated['expires_in_days'] ?? 7;
        
        $token = Str::random(64);
        
        $invitation = ArtistInvitation::create([
            'email' => $validated['email'] ?? null,
            'token' => $token,
            'expires_at' => now()->addDays((int) $expiresInDays),
            'created_by' => Auth::id(),
        ]);

        $registrationUrl = config('app.frontend_url') . '/artist-register?token=' . $token;

        return response()->json([
            'success' => true,
            'message' => 'Mã mời đã được tạo thành công.',
            'data' => [
                'invitation' => $invitation,
                'registration_url' => $registrationUrl
            ]
        ], 201);
    }

    /**
     * [API-ADM-32] Hủy mã mời
     */
    public function destroy($id): JsonResponse
    {
        $invitation = ArtistInvitation::findOrFail($id);

        // [RULE-INV-02] Không thể xóa mã đã được sử dụng
        if ($invitation->used_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thu hồi mã mời đã được sử dụng.'
            ], 403);
        }

        $invitation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã thu hồi và xóa mã mời.'
        ]);
    }
}
