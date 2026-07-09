<?php

namespace Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListeningHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'song_id', 'last_position', 'listened_at'];
    protected function casts(): array { return ['listened_at' => 'datetime']; }

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function song() { return $this->belongsTo(\Modules\Music\Models\Song::class); }
}
