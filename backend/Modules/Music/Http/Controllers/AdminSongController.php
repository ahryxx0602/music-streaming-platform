<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Music\Models\Song;

class AdminSongController extends Controller
{
    /**
     * [API-360] Get list of songs (Paginated & Filtered)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Song::with(['artist', 'genre']);

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('artist_id')) {
            $query->where('artist_id', $request->artist_id);
        }

        $songs = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $songs
        ]);
    }

    /**
     * [API-ADM-27] Generate S3 Pre-signed URL for direct upload
     */
    public function generatePresignedUrl(Request $request): JsonResponse
    {
        $request->validate([
            'file_name' => 'required|string',
            'content_type' => 'required|string',
            'file_size' => 'required|integer',
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
            'message' => 'Tạo S3 Presigned URL thành công.',
            'data' => [
                'upload_url' => $uploadUrl,
                's3_key' => $s3Key,
                'expires_in' => 600,
            ]
        ], 201);
    }

    /**
     * [API-ADM-28] Save Song Metadata after S3 Upload
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist_id' => 'required|exists:artist_profiles,id',
            'genre_id' => 'required|exists:genres,id',
            's3_key' => 'required|string',
        ]);

        $song = Song::create([
            'title' => $validated['title'],
            'artist_id' => $validated['artist_id'],
            'genre_id' => $validated['genre_id'],
            'original_file_path' => $validated['s3_key'],
            'status' => 'Approved', // Auto approve when uploaded by Admin
            'uploader_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        // 2. Kích hoạt Job xử lý Audio FFmpeg ngầm
        \Modules\Music\Jobs\ProcessAudioJob::dispatch($song);

        return response()->json([
            'success' => true,
            'message' => 'Lưu thông tin bài hát thành công. Đã gán trạng thái Approved và bắt đầu xử lý Audio.',
            'data' => [
                'song' => $song
            ]
        ], 201);
    }

    /**
     * [API-ADM-xx] Get unassigned songs for an artist
     */
    public function unassigned(Request $request): JsonResponse
    {
        $request->validate([
            'artist_id' => 'required|exists:artist_profiles,id'
        ]);

        $songs = Song::where('artist_id', $request->artist_id)
            ->whereNull('album_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $songs
        ]);
    }
}
