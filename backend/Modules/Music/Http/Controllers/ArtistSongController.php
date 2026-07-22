<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Music\Models\Song;

class ArtistSongController extends Controller
{
    /**
     * [API-ART-01] Generate S3 Pre-signed URL for direct upload
     */
    public function generatePresignedUrl(Request $request): JsonResponse
    {
        $request->validate([
            'file_name' => 'required|string',
            'content_type' => 'required|string',
        ]);

        $uuid = Str::uuid()->toString();
        $extension = pathinfo($request->file_name, PATHINFO_EXTENSION);
        $s3Key = 'songs/raw/' . date('Y/m') . '/' . $uuid . '.' . $extension;

        $client = Storage::disk('s3')->getClient();
        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $s3Key,
            'ContentType' => $request->content_type,
            'ACL' => 'private',
        ]);

        $presignedRequest = $client->createPresignedRequest($command, '+10 minutes');
        $uploadUrl = (string) $presignedRequest->getUri();

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $uploadUrl,
                'path' => $s3Key,
            ]
        ]);
    }

    /**
     * [API-ART-02] Save Song Metadata after S3 Upload
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'audio_path' => 'required|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'lyrics' => 'nullable|string',
            'album_id' => 'nullable|integer', // should validate exists in albums
        ]);

        // Get artist profile for current user
        $artistProfile = $request->user()->artistProfile;
        if (!$artistProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản chưa có Artist Profile.'
            ], 403);
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        $song = Song::create([
            'title' => $validated['title'],
            'artist_id' => $artistProfile->id,
            'genre_id' => $validated['genre_id'],
            'original_file_path' => $validated['audio_path'],
            'cover_image_url' => $coverPath,
            'status' => 'pending',
            'processing_status' => 'pending',
            'lyrics' => $validated['lyrics'] ?? null,
            'album_id' => $validated['album_id'] ?? null,
            'duration' => 0, // default, will be updated by FFmpeg
        ]);

        // Dispatch Job to process audio
        \Modules\Music\Jobs\ProcessAudioJob::dispatch($song);

        return response()->json([
            'success' => true,
            'message' => 'Metadata đã được lưu. Đang xử lý âm thanh ngầm.',
            'data' => [
                'id' => $song->id,
                'title' => $song->title,
                'status' => $song->status,
                'processing_status' => $song->processing_status,
            ]
        ], 201);
    }
}
