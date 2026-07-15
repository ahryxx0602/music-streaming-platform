<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'status',
        'notification_prefs',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_prefs' => 'array',
        ];
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            // Kiểm tra xem avatar đã là URL hợp lệ chưa (nếu lỡ lưu URL cũ)
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            return \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($this->avatar);
        }
        return null;
    }

    public function artistProfile() { return $this->hasOne(\Modules\Artist\Models\ArtistProfile::class); }
    public function playlists() { return $this->hasMany(\Modules\Playlist\Models\Playlist::class); }
    public function listeningHistories() { return $this->hasMany(\Modules\Analytics\Models\ListeningHistory::class); }
    public function favoriteSongs() { return $this->hasMany(\Modules\Users\Models\FavoriteSong::class); }
    public function favoriteAlbums() { return $this->hasMany(\Modules\Users\Models\FavoriteAlbum::class); }
    public function favoriteArtists() { return $this->hasMany(\Modules\Users\Models\FavoriteArtist::class); }
    public function favoritePlaylists() { return $this->hasMany(\Modules\Users\Models\FavoritePlaylist::class); }
    public function songs() { return $this->hasManyThrough(\Modules\Music\Models\Song::class, \Modules\Artist\Models\ArtistProfile::class, 'user_id', 'artist_id', 'id', 'id'); }
    public function comments() { return $this->hasMany(\Modules\Music\Models\Comment::class); }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }
}
