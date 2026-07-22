<?php

namespace Modules\Music\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\Auditable;

class Album extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes, Auditable;

    protected $fillable = ['artist_id', 'title', 'cover_image', 'release_date', 'type', 'status', 'description'];
    protected function casts(): array { return ['release_date' => 'date']; }

    public function artist() { return $this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
    public function songs() { return $this->hasMany(\Modules\Music\Models\Song::class); }
}
