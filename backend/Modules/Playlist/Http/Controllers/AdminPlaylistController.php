<?php

namespace Modules\Playlist\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Playlist\Http\Requests\StoreAdminPlaylistRequest;
use Modules\Playlist\Http\Requests\UpdateAdminPlaylistRequest;
use Modules\Playlist\Models\Playlist;

class AdminPlaylistController extends Controller
{
    /**
     * [API-ADM-xx] Get list of system playlists
     */
    public function index(Request $request): JsonResponse
    {
        $query = Playlist::where('type', 'system')->withCount('songs');

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $playlists = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $playlists
        ]);
    }

    /**
     * [API-ADM-xx] Store a newly created system playlist
     */
    public function store(StoreAdminPlaylistRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $playlist = Playlist::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'privacy' => $validated['privacy'],
            'type' => 'system',
            'user_id' => Auth::id(),
        ]);

        if (isset($validated['song_ids']) && is_array($validated['song_ids'])) {
            $insertData = [];
            foreach ($validated['song_ids'] as $index => $songId) {
                $insertData[] = [
                    'playlist_id' => $playlist->id,
                    'song_id' => $songId,
                    'position' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insertData)) {
                DB::table('playlist_songs')->insert($insertData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo Playlist hệ thống thành công.',
            'data' => [
                'playlist' => $playlist
            ]
        ], 201);
    }

    /**
     * [API-ADM-xx] Update the specified system playlist
     */
    public function update(UpdateAdminPlaylistRequest $request, $id): JsonResponse
    {
        $playlist = Playlist::where('type', 'system')->findOrFail($id);
        $validated = $request->validated();

        $playlist->update([
            'title' => $validated['title'] ?? $playlist->title,
            'description' => array_key_exists('description', $validated) ? $validated['description'] : $playlist->description,
            'privacy' => $validated['privacy'] ?? $playlist->privacy,
        ]);

        if (isset($validated['song_ids']) && is_array($validated['song_ids'])) {
            DB::table('playlist_songs')->where('playlist_id', $playlist->id)->delete();

            $insertData = [];
            foreach ($validated['song_ids'] as $index => $songId) {
                $insertData[] = [
                    'playlist_id' => $playlist->id,
                    'song_id' => $songId,
                    'position' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insertData)) {
                DB::table('playlist_songs')->insert($insertData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Playlist thành công.',
            'data' => [
                'playlist' => $playlist
            ]
        ]);
    }

    /**
     * [API-ADM-xx] Remove the specified system playlist (Soft Delete)
     */
    public function destroy($id): JsonResponse
    {
        $playlist = Playlist::where('type', 'system')->findOrFail($id);
        
        $playlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa Playlist thành công.'
        ]);
    }
}
