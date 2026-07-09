<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoriteArtist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'artist_id'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function artist() { return $this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
}
