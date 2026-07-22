<?php

namespace Modules\Artist\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Music\Models\Song;

class ArtistDashboardController extends Controller
{
    /**
     * Get overview stats for the artist dashboard.
     */
    public function stats(Request $request)
    {
        $user = Auth::user();
        
        // Get artist profile for current user
        $artistProfile = \Illuminate\Support\Facades\DB::table('artist_profiles')
            ->where('user_id', $user->id)
            ->first();
            
        $artistId = $artistProfile ? $artistProfile->id : 0;
        
        $songsQuery = Song::where('artist_id', $artistId);
        
        $totalTracks = $songsQuery->count();
        $totalStreams = $songsQuery->sum('play_count');
        
        // Balance calculation (mocking $0.01 per stream)
        $balanceUsd = $totalStreams * 0.01;
        
        // Mock followers for now
        $totalFollowers = rand(1000, 50000); // 45.2K mock in UI

        return response()->json([
            'success' => true,
            'data' => [
                'total_followers' => $totalFollowers,
                'total_streams' => $totalStreams,
                'total_tracks' => $totalTracks,
                'balance_usd' => round($balanceUsd, 2)
            ]
        ]);
    }

    /**
     * Get top tracks for the artist.
     */
    public function topTracks(Request $request)
    {
        $user = Auth::user();
        
        // Get artist profile for current user
        $artistProfile = \Illuminate\Support\Facades\DB::table('artist_profiles')
            ->where('user_id', $user->id)
            ->first();
            
        $artistId = $artistProfile ? $artistProfile->id : 0;
        
        $topTracks = Song::where('artist_id', $artistId)
            ->orderBy('play_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($song) {
                return [
                    'id' => $song->id,
                    'title' => $song->title,
                    'streams' => $song->play_count,
                    'cover' => $song->cover_image_url ?: 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=100&q=80',
                    'trend' => '+' . rand(1, 30) . '%'
                ];
            });
            
        // If no tracks, return some mocks for visual testing
        if ($topTracks->isEmpty()) {
            $topTracks = collect([
                [
                    'id' => 1,
                    'title' => 'Midnight City (Mock)',
                    'streams' => 450230,
                    'cover' => 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=100&q=80',
                    'trend' => '+24%'
                ],
                [
                    'id' => 2,
                    'title' => 'Neon Lights (Mock)',
                    'streams' => 320105,
                    'cover' => 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?w=100&q=80',
                    'trend' => '+15%'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $topTracks
        ]);
    }

    /**
     * Get streams chart data.
     */
    public function streamsChart(Request $request)
    {
        // Mock 30 days data
        $data = [];
        for ($i = 1; $i <= 30; $i++) {
            $data[] = rand(10000, 50000);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
