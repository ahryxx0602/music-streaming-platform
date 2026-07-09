<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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

    public function artistProfile() { return $this->hasOne(\Modules\Artist\Models\ArtistProfile::class); }
    public function playlists() { return $this->hasMany(\Modules\Playlist\Models\Playlist::class); }
    public function listeningHistories() { return $this->hasMany(\Modules\Analytics\Models\ListeningHistory::class); }
    public function favoriteSongs() { return $this->hasMany(\Modules\Users\Models\FavoriteSong::class); }
    public function favoriteAlbums() { return $this->hasMany(\Modules\Users\Models\FavoriteAlbum::class); }
    public function favoriteArtists() { return $this->hasMany(\Modules\Users\Models\FavoriteArtist::class); }
    public function favoritePlaylists() { return $this->hasMany(\Modules\Users\Models\FavoritePlaylist::class); }
    public function comments() { return $this->hasMany(\Modules\Music\Models\Comment::class); }
}
