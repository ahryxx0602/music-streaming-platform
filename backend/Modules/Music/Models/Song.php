<?php

namespace Modules\Music\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Song extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['artist_id', 'album_id', 'genre_id', 'title', 'lyrics', 'duration', 'original_file_path', 'hls_path', 'original_size', 'bitrate', 'sample_rate', 'channels', 'checksum', 'cover_image', 'status', 'play_count', 'rejected_reason', 'rejected_at', 'approved_at', 'processing_error', 'processing_attempts', 'uploader_id'];
    protected function casts(): array { return ['rejected_at' => 'datetime', 'approved_at' => 'datetime']; }

    public function artist() { return $this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
    public function album() { return $this->belongsTo(\Modules\Music\Models\Album::class); }
    public function genre() { return $this->belongsTo(\Modules\Music\Models\Genre::class); }
    public function featuredArtists() { return $this->belongsToMany(\Modules\Artist\Models\ArtistProfile::class, 'song_artists', 'song_id', 'artist_id')->withPivot('role')->withTimestamps(); }
    public function playlists() { return $this->belongsToMany(\Modules\Playlist\Models\Playlist::class, 'playlist_songs')->withPivot('position')->withTimestamps(); }
    public function comments() { return $this->hasMany(\Modules\Music\Models\Comment::class); }
    public function streams() { return $this->hasMany(\Modules\Analytics\Models\Stream::class); }
}
