<?php

namespace App\Traits;

use Modules\Administration\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAction($model, 'created');
        });

        static::updated(function ($model) {
            self::logAction($model, 'updated');
        });

        static::deleted(function ($model) {
            self::logAction($model, 'deleted');
        });
    }

    protected static function logAction($model, $action)
    {
        // Don't log if running in console (e.g. migrations/seeders) unless there is an authenticated user
        if (app()->runningInConsole() && !Auth::check()) {
            return;
        }

        $oldValues = $action === 'updated' ? $model->getOriginal() : null;
        $newValues = $action !== 'deleted' ? $model->getAttributes() : null;

        // Mask passwords or sensitive fields if necessary
        if ($oldValues && isset($oldValues['password'])) unset($oldValues['password']);
        if ($newValues && isset($newValues['password'])) unset($newValues['password']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => get_class($model),
            'entity_id' => $model->getKey(),
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => Request::ip()
        ]);
    }
}
