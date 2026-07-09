<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoriteAlbum extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'album_id'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function album() { return $this->belongsTo(\Modules\Music\Models\Album::class); }
}
