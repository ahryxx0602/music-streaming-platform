<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Music\Models\Song;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ListenerSongController extends Controller
{
    /**
     * Get song details for the player
     */
    public function show($id): JsonResponse
    {
        $song = Song::with(['artist:id,stage_name,avatar_url', 'album:id,title,cover_image', 'genre:id,name'])
            ->findOrFail($id);

        // Generate full URLs if they are relative paths
        $audioUrl = $song->original_file_path;
        if ($audioUrl && !filter_var($audioUrl, FILTER_VALIDATE_URL)) {
            $audioUrl = Storage::disk(config('filesystems.default'))->url($audioUrl);
        }

        $coverUrl = $song->cover_image;
        if ($coverUrl && !filter_var($coverUrl, FILTER_VALIDATE_URL)) {
            $coverUrl = Storage::disk('public')->url($coverUrl);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $song->id,
                'title' => $song->title,
                'artist_name' => $song->artist ? $song->artist->stage_name : null,
                'album_name' => $song->album ? $song->album->title : null,
                'genre' => $song->genre ? $song->genre->name : null,
                'cover_url' => $coverUrl,
                'audio_url' => $audioUrl,
                'duration' => $song->duration,
                'play_count' => $song->play_count,
            ]
        ]);
    }

    /**
     * Track a play for a song (Rate limited in routes)
     */
    public function trackPlay(Request $request, $id): JsonResponse
    {
        $song = Song::with('artist')->findOrFail($id);
        
        // Cần đảm bảo user không cày view (có thể dùng rate limiter ở route `throttle:1,60` cho 1 IP/phút)
        // Tuy nhiên, có thể check log trong bảng play_histories nếu cần thiết
        
        // Tăng play_count của bài hát
        $song->increment('play_count');
        
        // Tăng total_streams của artist
        if ($song->artist) {
            $song->artist->increment('total_streams');
        }

        return response()->json([
            'success' => true,
            'message' => 'Lượt nghe đã được ghi nhận',
            'data' => [
                'play_count' => $song->play_count
            ]
        ]);
    }
}
