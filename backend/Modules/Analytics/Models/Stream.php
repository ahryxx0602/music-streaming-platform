<?php

namespace Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'song_id', 'device', 'ip_address', 'session_id', 'duration', 'streamed_at'];
    protected function casts(): array { return ['streamed_at' => 'datetime']; }

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function song() { return $this->belongsTo(\Modules\Music\Models\Song::class); }
}
