<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait AuditableTrait
{
    /**
     * Log a create action.
     */
    protected function logCreated(Model $model, ?string $description = null): void
    {
        AuditLog::log([
            'action' => 'create',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'new_values' => $model->toArray(),
            'description' => $description ?? 'Created '.class_basename($model)." #{$model->id}",
        ]);
    }

    /**
     * Log an update action.
     */
    protected function logUpdated(Model $model, array $oldAttributes, ?string $description = null): void
    {
        AuditLog::log([
            'action' => 'update',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldAttributes,
            'new_values' => $model->toArray(),
            'description' => $description ?? 'Updated '.class_basename($model)." #{$model->id}",
        ]);
    }

    /**
     * Log a delete action.
     */
    protected function logDeleted(Model $model, ?string $description = null): void
    {
        AuditLog::log([
            'action' => 'delete',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $model->toArray(),
            'description' => $description ?? 'Deleted '.class_basename($model)." #{$model->id}",
        ]);
    }

    /**
     * Log a custom action.
     */
    protected function logAction(string $action, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): void
    {
        AuditLog::log([
            'action' => $action,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
        ]);
    }
}
