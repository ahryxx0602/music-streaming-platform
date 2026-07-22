<?php

namespace Modules\Artist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArtistProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'stage_name', 'bio', 'cover_image', 'is_verified', 'verified_at', 'facebook', 'instagram', 'youtube', 'website', 'contact_email', 'avatar_url', 'banner_url', 'social_links'];
    protected function casts(): array { return ['is_verified' => 'boolean', 'verified_at' => 'datetime', 'social_links' => 'json']; }

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function albums() { return $this->hasMany(\Modules\Music\Models\Album::class, 'artist_id'); }
    public function songs() { return $this->hasMany(\Modules\Music\Models\Song::class, 'artist_id'); }
    public function followers() { return $this->hasMany(\Modules\Artist\Models\Follow::class, 'artist_id'); }
}
