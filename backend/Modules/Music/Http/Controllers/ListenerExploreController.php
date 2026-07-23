<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Music\Models\Song;
use Modules\Artist\Models\ArtistProfile;
use Modules\Administration\Models\Banner;
use Modules\Playlist\Models\Playlist;

class ListenerExploreController extends Controller
{
    /**
     * Lấy dữ liệu tổng hợp cho trang chủ Khám phá
     */
    public function index(): JsonResponse
    {
        $cacheTTL = 60 * 60; // 60 minutes

        $data = Cache::remember('home_explore_data', $cacheTTL, function () {
            
            // 1. Lấy Banners (active, sắp xếp theo order)
            $bannersRaw = Banner::where('is_active', true)
                ->orderBy('order')
                ->get(['id', 'title', 'image_url', 'target_url']);
                
            $banners = $bannersRaw->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'image_url' => $this->formatUrl($banner->image_url),
                    'link_url' => $banner->target_url
                ];
            });

            // 2. Lấy New Releases (Top 10 bài hát mới nhất, published)
            $newReleasesRaw = Song::with('artist:id,stage_name')
                ->where('status', 'published')
                ->latest('created_at')
                ->take(10)
                ->get(['id', 'title', 'artist_id', 'cover_image', 'play_count']);

            $newReleases = $newReleasesRaw->map(function ($song) {
                return [
                    'id' => $song->id,
                    'title' => $song->title,
                    'artist' => $song->artist ? [
                        'id' => $song->artist->id,
                        'stage_name' => $song->artist->stage_name
                    ] : null,
                    'cover_url' => $this->formatUrl($song->cover_image),
                    'play_count' => $song->play_count
                ];
            });

            // 3. Lấy Trending Artists (Top 5 nghệ sĩ nhiều stream nhất)
            $trendingArtistsRaw = ArtistProfile::orderByDesc('total_streams')
                ->take(5)
                ->get(['id', 'stage_name', 'avatar_url', 'total_streams']);

            $trendingArtists = $trendingArtistsRaw->map(function ($artist) {
                return [
                    'id' => $artist->id,
                    'stage_name' => $artist->stage_name,
                    'avatar_url' => $this->formatUrl($artist->avatar_url),
                ];
            });

            // 4. Lấy Featured Playlists (4 System Playlists mới nhất)
            $featuredPlaylistsRaw = Playlist::where('type', 'system')
                ->latest()
                ->take(4)
                ->get(['id', 'title', 'cover_image']);

            $featuredPlaylists = $featuredPlaylistsRaw->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'name' => $playlist->title,
                    'cover_url' => $this->formatUrl($playlist->cover_image)
                ];
            });

            return [
                'banners' => $banners,
                'new_releases' => $newReleases,
                'trending_artists' => $trendingArtists,
                'featured_playlists' => $featuredPlaylists
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Helper to format relative paths to full URLs
     */
    private function formatUrl(?string $path): ?string
    {
        if (empty($path)) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        return Storage::disk('public')->url($path);
    }
}
