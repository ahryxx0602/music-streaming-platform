<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Music\Models\Album;
use Modules\Music\Models\Song;

class ArtistAlbumController extends Controller
{
    /**
     * [API-ART-03] Lấy danh sách Album
     */
    public function index(Request $request): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;
        if (!$artistProfile) return response()->json(['success' => false, 'message' => 'Not an artist'], 403);

        $albums = Album::where('artist_id', $artistProfile->id)
            ->withCount('songs')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $albums
        ]);
    }

    /**
     * [API-ART-04] Tạo Album mới
     */
    public function store(Request $request): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;
        if (!$artistProfile) return response()->json(['success' => false, 'message' => 'Not an artist'], 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:Single,EP,Album',
            'release_date' => 'nullable|date',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'song_ids' => 'nullable|array',
            'song_ids.*' => 'integer|exists:songs,id'
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('albums', 'public');
        }

        $album = Album::create([
            'artist_id' => $artistProfile->id,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'release_date' => $validated['release_date'] ?? null,
            'description' => $validated['description'] ?? null,
            'cover_image' => $coverPath,
            'status' => 'Draft' // Default status
        ]);

        if (!empty($validated['song_ids'])) {
            $upsertData = [];
            foreach ($validated['song_ids'] as $index => $songId) {
                $song = Song::where('id', $songId)->where('artist_id', $artistProfile->id)->first();
                if ($song) {
                    $upsertData[] = array_merge($song->getAttributes(), [
                        'album_id' => $album->id,
                        'track_number' => $index + 1
                    ]);
                }
            }
            if (count($upsertData) > 0) {
                Song::upsert($upsertData, ['id'], ['album_id', 'track_number']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo Album thành công.',
            'data' => $album
        ], 201);
    }

    /**
     * [API-ART-05] Xem chi tiết Album
     */
    public function show(Request $request, $id): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;
        if (!$artistProfile) return response()->json(['success' => false, 'message' => 'Not an artist'], 403);

        $album = Album::with(['songs' => function ($q) {
            $q->orderBy('track_number', 'asc');
        }])->where('id', $id)->where('artist_id', $artistProfile->id)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $album
        ]);
    }

    /**
     * [API-ART-06] Cập nhật thông tin Album
     */
    public function update(Request $request, $id): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;
        if (!$artistProfile) return response()->json(['success' => false, 'message' => 'Not an artist'], 403);

        $album = Album::where('id', $id)->where('artist_id', $artistProfile->id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:Single,EP,Album',
            'release_date' => 'nullable|date',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($album->cover_image && Storage::disk('public')->exists($album->cover_image)) {
                Storage::disk('public')->delete($album->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('albums', 'public');
        }

        $album->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Album thành công.',
            'data' => $album
        ]);
    }

    /**
     * [API-ART-07] Reorder Bài hát trong Album
     */
    public function reorderSongs(Request $request, $id): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;
        if (!$artistProfile) return response()->json(['success' => false, 'message' => 'Not an artist'], 403);

        $album = Album::where('id', $id)->where('artist_id', $artistProfile->id)->firstOrFail();

        $validated = $request->validate([
            'song_ids' => 'required|array',
            'song_ids.*' => 'integer|exists:songs,id'
        ]);

        $upsertData = [];
        foreach ($validated['song_ids'] as $index => $songId) {
            $song = Song::where('id', $songId)->where('album_id', $album->id)->where('artist_id', $artistProfile->id)->first();
            if ($song) {
                $upsertData[] = array_merge($song->getAttributes(), [
                    'track_number' => $index + 1
                ]);
            }
        }

        if (count($upsertData) > 0) {
            Song::upsert($upsertData, ['id'], ['track_number']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thứ tự bài hát thành công.'
        ]);
    }

    /**
     * [API-ART-08] Xóa Album
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $artistProfile = $request->user()->artistProfile;
        if (!$artistProfile) return response()->json(['success' => false, 'message' => 'Not an artist'], 403);

        $album = Album::where('id', $id)->where('artist_id', $artistProfile->id)->firstOrFail();

        // Gỡ album_id của tất cả bài hát thuộc album này
        Song::where('album_id', $album->id)->update(['album_id' => null, 'track_number' => 0]);

        $album->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa Album thành công.'
        ]);
    }
}
