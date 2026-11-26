<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\Activity;
use Spatie\Activitylog\LogOptions;

trait HasActivityLog
{
    /**
     * Boot the trait.
     */
    public static function bootHasActivityLog(): void
    {
        static::created(function (Model $model) {
            activity()
                ->performedOn($model)
                ->event('created')
                ->withProperties([
                    'attributes' => $model->toArray(),
                ])
                ->log('created ' . class_basename($model) . ' record');
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            
            // Filter out timestamps
            $changes = array_filter($changes, function ($key) {
                return !in_array($key, ['created_at', 'updated_at']);
            }, ARRAY_FILTER_USE_KEY);
            
            if (!empty($changes)) {
                $old = [];
                foreach (array_keys($changes) as $key) {
                    $old[$key] = $model->getOriginal($key);
                }

                activity()
                    ->performedOn($model)
                    ->event('updated')
                    ->withProperties([
                        'old' => $old,
                        'attributes' => $changes,
                    ])
                    ->log('updated ' . class_basename($model) . ' record');
            }
        });

        static::deleted(function (Model $model) {
            activity()
                ->performedOn($model)
                ->event('deleted')
                ->withProperties([
                    'attributes' => $model->toArray(),
                ])
                ->log('deleted ' . class_basename($model) . ' record');
        });
    }

    /**
     * Get the activity log for this model.
     */
    public function activities()
    {
        return $this->morphMany(config('activitylog.activity_model'), 'subject');
    }

    /**
     * Get the latest activity for this model.
     */
    public function latestActivity()
    {
        return $this->activities()->latest()->first();
    }

    /**
     * Get activities filtered by event type.
     */
    public function activitiesByEvent(string $event)
    {
        return $this->activities()->where('event', $event);
    }

    /**
     * Get activities within a date range.
     */
    public function activitiesBetween($startDate, $endDate)
    {
        return $this->activities()
            ->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Log a custom activity for this model.
     */
    public function logActivity(string $description, array $properties = [])
    {
        return activity()
            ->performedOn($this)
            ->withProperties($properties)
            ->log($description);
    }

    /**
     * Disable activity logging for this model.
     */
    public static function disableActivityLogging(): void
    {
        static::withoutEvents(function () {
            // This will prevent the boot method from firing
        });
    }
}
