<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecentSearch extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'keyword'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
}
