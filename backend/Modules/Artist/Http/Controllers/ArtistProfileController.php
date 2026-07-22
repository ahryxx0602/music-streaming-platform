<?php

namespace Modules\Artist\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Artist\Models\ArtistProfile;

class ArtistProfileController extends Controller
{
    /**
     * [API-ART-01] Xem Profile
     */
    public function show(Request $request): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;
        
        if (!$artistProfile) {
            return response()->json(['success' => false, 'message' => 'Profile không tồn tại'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $artistProfile
        ]);
    }

    /**
     * [API-ART-02] Cập nhật Profile
     */
    public function update(Request $request): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;

        if (!$artistProfile) {
            return response()->json(['success' => false, 'message' => 'Profile không tồn tại'], 404);
        }

        $validated = $request->validate([
            'stage_name' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'social_links' => 'nullable|json' // Expecting JSON string from frontend if formData is used
        ]);

        if (isset($validated['social_links'])) {
            $validated['social_links'] = json_decode($validated['social_links'], true);
        }

        // Xử lý avatar
        if ($request->hasFile('avatar')) {
            if ($artistProfile->avatar_url && Storage::disk('public')->exists($artistProfile->avatar_url)) {
                Storage::disk('public')->delete($artistProfile->avatar_url);
            }
            $validated['avatar_url'] = $request->file('avatar')->store('artist_avatars', 'public');
        }

        // Xử lý banner
        if ($request->hasFile('banner')) {
            if ($artistProfile->banner_url && Storage::disk('public')->exists($artistProfile->banner_url)) {
                Storage::disk('public')->delete($artistProfile->banner_url);
            }
            $validated['banner_url'] = $request->file('banner')->store('artist_banners', 'public');
        }

        $artistProfile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Profile thành công',
            'data' => $artistProfile->fresh()
        ]);
    }
}
