<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait RecordsActivity
{
    public static function bootRecordsActivity(): void
    {
        static::created(fn (Model $model) => $model->recordActivity('created', null, $model->getAttributes()));

        static::updated(function (Model $model) {
            $changes = collect($model->getChanges())
                ->except(['updated_at'])
                ->all();

            if ($changes === []) {
                return;
            }

            $before = collect($model->getOriginal())
                ->only(array_keys($changes))
                ->all();

            $model->recordActivity('updated', $before, $changes);
        });

        static::deleted(fn (Model $model) => $model->recordActivity('deleted', $model->getOriginal(), null));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $model) => $model->recordActivity('restored', null, $model->getAttributes()));
        }
    }

    public function recordActivity(string $action, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'auditable_label' => $this->activityLabel(),
            'before_values' => $before,
            'after_values' => $after,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    private function activityLabel(): ?string
    {
        foreach (['titulo', 'nombre', 'asunto', 'email', 'slug'] as $attribute) {
            if (! empty($this->{$attribute})) {
                return $this->{$attribute};
            }
        }

        return null;
    }
}
