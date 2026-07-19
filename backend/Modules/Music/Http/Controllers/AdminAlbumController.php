<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Music\Http\Requests\StoreAlbumRequest;
use Modules\Music\Http\Requests\UpdateAlbumRequest;
use Modules\Music\Models\Album;
use Modules\Music\Models\Song;

class AdminAlbumController extends Controller
{
    /**
     * [API-ADM-xx] Get list of albums
     */
    public function index(Request $request): JsonResponse
    {
        $query = Album::with('artist')->withCount('songs');

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('artist_id')) {
            $query->where('artist_id', $request->artist_id);
        }

        $albums = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $albums
        ]);
    }

    /**
     * [API-ADM-xx] Store a newly created album
     */
    public function store(StoreAlbumRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('albums/covers', 'public');
            $validated['cover_image'] = $path;
        }

        $validated['status'] = 'Approved'; // Auto approved for Admin

        $album = Album::create($validated);

        if (isset($validated['song_ids']) && is_array($validated['song_ids'])) {
            // Unassign from other albums just in case (though rule says max 1 album, foreign key handles it, 
            // but we want to ensure we don't break anything, just update the song directly)
            Song::whereIn('id', $validated['song_ids'])->update(['album_id' => $album->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo Album thành công.',
            'data' => [
                'album' => $album
            ]
        ], 201);
    }

    /**
     * [API-ADM-xx] Update the specified album
     */
    public function update(UpdateAlbumRequest $request, $id): JsonResponse
    {
        $album = Album::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($album->cover_image && Storage::disk('public')->exists($album->cover_image)) {
                Storage::disk('public')->delete($album->cover_image);
            }
            $path = $request->file('cover_image')->store('albums/covers', 'public');
            $validated['cover_image'] = $path;
        }

        $album->update($validated);

        if (isset($validated['song_ids']) && is_array($validated['song_ids'])) {
            // RULE-ALB-01: Update new assignment
            // 1. Remove old songs from this album
            Song::where('album_id', $album->id)->update(['album_id' => null]);
            // 2. Assign new ones
            if (count($validated['song_ids']) > 0) {
                Song::whereIn('id', $validated['song_ids'])->update(['album_id' => $album->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Album thành công.',
            'data' => [
                'album' => $album
            ]
        ]);
    }

    /**
     * [API-ADM-xx] Remove the specified album (Soft Delete)
     */
    public function destroy($id): JsonResponse
    {
        $album = Album::findOrFail($id);

        // RULE-ALB-02: Gỡ tất cả bài hát trước khi xóa mềm Album
        Song::where('album_id', $album->id)->update(['album_id' => null]);

        $album->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa Album thành công.'
        ]);
    }
}
