<?php

namespace Modules\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'old_values', 'new_values', 'ip_address'];
    protected function casts(): array { return ['old_values' => 'array', 'new_values' => 'array']; }

    public function user() { return $this->belongsTo(\App\Models\User::class); }
}
