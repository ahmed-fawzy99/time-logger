<?php

namespace App\Traits\Api;

use DB;

trait HasCacheControl
{
    use CacheRequest;

    public static function bootHasCacheControl(): void
    {
        $flushCache = function ($model) {
            $tags = $model->getModelCacheTags(class_basename($model));
            $model->flushItemCache($tags);
        };

        // Defer cache operations until after transaction commits to avoid PgBouncer connection pooling issues
        static::created(function ($model) use ($flushCache) {
            DB::afterCommit(function () use ($model, $flushCache) {
                $flushCache($model);
            });
        });

        static::updated($flushCache);
        static::deleted($flushCache);
    }
}
