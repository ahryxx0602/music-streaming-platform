<?php

namespace Modules\Music\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'slug', 'icon', 'description', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->with('children'); }
    public function songs() { return $this->hasMany(\Modules\Music\Models\Song::class); }
}
