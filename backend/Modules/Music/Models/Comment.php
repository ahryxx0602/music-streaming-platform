<?php

namespace Modules\Music\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['user_id', 'song_id', 'parent_id', 'content'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function song() { return $this->belongsTo(\Modules\Music\Models\Song::class); }
    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function replies() { return $this->hasMany(self::class, 'parent_id'); }
}
