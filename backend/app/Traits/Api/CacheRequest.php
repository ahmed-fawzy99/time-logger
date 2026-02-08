<?php

namespace App\Traits\Api;

use App\Enums\CacheTagEnum;
use App\Http\Filters\Api\V1\QueryFilter;
use Closure;
use Illuminate\Support\Facades\Cache;

trait CacheRequest
{
    public const GLOBAL_TAGS = [
        CacheTagEnum::PREFERENCE->value,
    ];

    public const CONTENT_TAGS = [
        CacheTagEnum::PROJECT->value,
        CacheTagEnum::TIME_FRAME->value,
        CacheTagEnum::TIME_ENTRY->value,
    ];

    /**
     * Get connected types with IDs
     *
     * @param  array  $connectedTypes  Array of connected types
     * @param  array|null  $ids  Array of IDs to append to the connected types
     */
    private function getConnectedTypes(array $connectedTypes, ?array $ids = null): array
    {
        for ($index = 0; $index < count($connectedTypes); $index++) {
            if (isset($ids[$index])) {
                $connectedTypes[$index] = $connectedTypes[$index].':'.$ids[$index];
            }
        }

        return $connectedTypes;
    }

    /**
     * Cache key for all items
     */
    private function getItemsCacheKey(string $type, QueryFilter $filters, ?array $additionalFilters = null): string
    {
        if ($additionalFilters) {
            $filters->request->merge($additionalFilters);
        }

        // Only serialize the request parameters to avoid closure serialization issues
        $filterParams = $filters->request->all();
        ksort($filterParams); // Sort the parameters to ensure consistent cache keys

        return "{$type}s:".md5(http_build_query($filterParams));
    }

    /**
     * Cache key for a single item
     */
    private function getItemCacheKey(string $type, string $id, ?QueryFilter $filters = null): string
    {
        $filterPart = '';
        if ($filters) {
            // Only serialize the request parameters to avoid closure serialization issues
            $filterParams = $filters->request->all();
            ksort($filterParams); // Sort the parameters to ensure consistent cache keys
            $filterPart = ':'.md5(http_build_query($filterParams));
        }

        return "{$type}:{$id}{$filterPart}";
    }

    /**
     * Get cache tags for the resource type
     */
    private function getCacheTags(string $type, ?string $id = null): array
    {
        $tags = [$type, "{$type}s"];

        if ($id) {
            $tags[] = "{$type}:{$id}";
        }

        return $tags;
    }

    /**
     * Remember item in cache with proper tags
     *
     * @param  string  $key  Cache key
     * @param  array  $tags  Cache tags
     * @param  Closure  $callback  Callback that returns the value to cache
     * @param  int|null  $ttl  Time to live in seconds (null = forever)
     * @return mixed
     */
    private function cacheRemember(string $key, array $tags, Closure $callback, ?int $ttl = null)
    {
        if ($ttl === -1) {
            return Cache::tags($tags)->rememberForever($key, $callback);
        }

        return Cache::tags($tags)->remember($key, $ttl, $callback);
    }

    private function getModelCacheTags(string $modelClass): array
    {
        if (in_array($modelClass, self::CONTENT_TAGS)) {
            return self::CONTENT_TAGS;
        } else {
            // Global Tag. Clear all tags
            return array_merge(self::GLOBAL_TAGS, self::CONTENT_TAGS);
        }
    }

    /**
     * Flush all item caches for the given tags
     */
    private function flushItemCache(array $tags): void
    {
        Cache::tags($tags)->flush();
    }
}
