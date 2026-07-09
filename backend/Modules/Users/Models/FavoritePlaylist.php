<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoritePlaylist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'playlist_id'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function playlist() { return $this->belongsTo(\Modules\Playlist\Models\Playlist::class); }
}
