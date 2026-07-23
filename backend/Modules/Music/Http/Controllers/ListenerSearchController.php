<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Modules\Music\Models\Song;
use Modules\Artist\Models\ArtistProfile;
use Modules\Music\Models\Album;

class ListenerSearchController extends Controller
{
    /**
     * Tìm kiếm toàn cầu cho Khán giả (Songs, Artists, Albums)
     */
    public function index(Request $request): JsonResponse
    {
        $keyword = $request->get('q', '');

        // Trả về rỗng nếu keyword quá ngắn
        if (mb_strlen($keyword) < 2) {
            return response()->json([
                'success' => true,
                'data' => [
                    'songs' => [],
                    'artists' => [],
                    'albums' => []
                ]
            ]);
        }

        $searchPattern = '%' . $keyword . '%';

        // 1. Tìm kiếm Songs (Limit 10)
        $songsRaw = Song::with('artist:id,stage_name')
            ->where('title', 'LIKE', $searchPattern)
            ->where('status', 'published')
            ->take(10)
            ->get(['id', 'title', 'artist_id', 'cover_image', 'original_file_path', 'duration']);

        $songs = $songsRaw->map(function ($song) {
            return [
                'id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist ? [
                    'id' => $song->artist->id,
                    'stage_name' => $song->artist->stage_name
                ] : null,
                'cover_url' => $this->formatUrl($song->cover_image),
                'audio_url' => $this->formatUrl($song->original_file_path, true),
                'duration' => $song->duration
            ];
        });

        // 2. Tìm kiếm Artists (Limit 5)
        $artistsRaw = ArtistProfile::where('stage_name', 'LIKE', $searchPattern)
            ->orWhereHas('user', function ($query) use ($searchPattern) {
                $query->where('name', 'LIKE', $searchPattern);
            })
            ->take(5)
            ->get(['id', 'stage_name', 'avatar_url']);

        $artists = $artistsRaw->map(function ($artist) {
            return [
                'id' => $artist->id,
                'stage_name' => $artist->stage_name,
                'avatar_url' => $this->formatUrl($artist->avatar_url)
            ];
        });

        // 3. Tìm kiếm Albums (Limit 5)
        $albumsRaw = Album::with('artist:id,stage_name')
            ->where('title', 'LIKE', $searchPattern)
            ->where('status', 'published')
            ->take(5)
            ->get(['id', 'title', 'cover_image', 'artist_id']);

        $albums = $albumsRaw->map(function ($album) {
            return [
                'id' => $album->id,
                'title' => $album->title,
                'artist' => $album->artist ? [
                    'stage_name' => $album->artist->stage_name
                ] : null,
                'cover_url' => $this->formatUrl($album->cover_image)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'songs' => $songs,
                'artists' => $artists,
                'albums' => $albums
            ]
        ]);
    }

    /**
     * Helper to format URLs
     */
    private function formatUrl(?string $path, bool $isAudio = false): ?string
    {
        if (empty($path)) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        
        $disk = $isAudio ? config('filesystems.default') : 'public';
        return Storage::disk($disk)->url($path);
    }
}
