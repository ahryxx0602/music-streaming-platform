<?php

namespace App\Traits;

use App\Models\AuditLog;
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

        $oldValues = [];
        $newValues = [];

        if ($action === 'created') {
            $newValues = $model->getAttributes();
        } elseif ($action === 'updated') {
            $changes = $model->getChanges();
            $original = $model->getOriginal();
            
            foreach ($changes as $key => $value) {
                // Ignore tracking updated_at changes if nothing else changed
                if ($key === 'updated_at') continue;
                
                $oldValues[$key] = array_key_exists($key, $original) ? $original[$key] : null;
                $newValues[$key] = $value;
            }
            
            // If only updated_at changed, don't create an audit log
            if (empty($newValues)) return;
        } elseif ($action === 'deleted') {
            $oldValues = $model->getAttributes();
        }

        // Mask passwords or sensitive fields if necessary
        $sensitiveFields = ['password', 'remember_token'];
        foreach ($sensitiveFields as $field) {
            if (isset($oldValues[$field])) $oldValues[$field] = '********';
            if (isset($newValues[$field])) $newValues[$field] = '********';
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }
}
