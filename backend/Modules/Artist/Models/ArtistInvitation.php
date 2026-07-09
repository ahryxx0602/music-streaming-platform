<?php

namespace Modules\Artist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArtistInvitation extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'token', 'expires_at', 'used_at', 'created_by'];
    protected function casts(): array { return ['expires_at' => 'datetime', 'used_at' => 'datetime']; }

    public function createdBy() { return $this->belongsTo(\App\Models\User::class, 'created_by'); }
}
