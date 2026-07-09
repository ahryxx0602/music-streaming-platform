<?php

namespace Modules\Playlist\Models;

use \Illuminate\Database\Eloquent\Relations\Pivot;

class PlaylistSong extends Pivot
{
    use HasFactory;

    protected $fillable = ['playlist_id', 'song_id', 'position'];
}
