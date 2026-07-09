<?php

namespace Modules\Music\Models;

use \Illuminate\Database\Eloquent\Relations\Pivot;

class SongArtist extends Pivot
{
    use HasFactory;

    protected $fillable = ['song_id', 'artist_id', 'role'];
}
