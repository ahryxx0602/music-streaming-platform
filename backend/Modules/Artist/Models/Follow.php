<?php

namespace Modules\Artist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = ['listener_id', 'artist_id'];

    public function listener() { return $this->belongsTo(\App\Models\User::class, 'listener_id'); }
    public function artist() { return $this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
}
