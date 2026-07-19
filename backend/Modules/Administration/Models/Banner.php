<?php

namespace Modules\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'image_url', 'target_url', 'order', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }

    protected static function newFactory()
    {
        return \Modules\Administration\Database\factories\BannerFactory::new();
    }
}
