<?php

namespace Modules\Users\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadAvatarAction
{
    /**
     * Handle the avatar upload to default filesystem (MinIO/S3 or Public).
     *
     * @param User $user
     * @param UploadedFile $file
     * @return User
     */
    public function execute(User $user, UploadedFile $file): User
    {
        // 1. Nếu user đã có avatar cũ, tiến hành xóa trên Storage
        if ($user->avatar) {
            // Xử lý extract path từ URL nếu lưu URL đầy đủ
            // Tạm thời giả định lưu relative path
            Storage::disk(config('filesystems.default'))->delete($user->avatar);
        }

        // 2. Generate file name
        $extension = $file->getClientOriginalExtension();
        $fileName = 'avatars/' . $user->id . '_' . Str::random(10) . '.' . $extension;

        // 3. Upload file lên Storage (Mặc định là S3/MinIO nếu đã config .env)
        $path = $file->storeAs('avatars', basename($fileName), config('filesystems.default'));

        // Lấy relative path
        // 4. Update Database
        $user->avatar = $path;
        $user->save();

        return $user;
    }
}
