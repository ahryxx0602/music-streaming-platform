<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoriteSong extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'song_id'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function song() { return $this->belongsTo(\Modules\Music\Models\Song::class); }
}
