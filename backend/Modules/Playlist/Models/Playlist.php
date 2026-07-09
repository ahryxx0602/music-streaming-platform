<?php

namespace Modules\Playlist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Playlist extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['user_id', 'title', 'description', 'cover_image', 'privacy', 'type'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function songs() { return $this->belongsToMany(\Modules\Music\Models\Song::class, 'playlist_songs')->withPivot('position')->withTimestamps(); }
}
