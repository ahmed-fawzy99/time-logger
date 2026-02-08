<?php

declare(strict_types=1);

namespace App\Services\V1;

namespace App\Services\V1;

use App\Enums\CacheTagEnum;
use App\Helpers\C;
use App\Http\Filters\Api\V1\Filters\PreferenceFilter;
use App\Models\Preference;
use App\Traits\Api\CacheRequest;

class PreferenceServices
{
    use CacheRequest;

    private string $TYPE = CacheTagEnum::PREFERENCE->value;

    private int $TTL = C::TWELVE_HOURS;

    /**
     * Get all Preferences with pagination and filtering
     *
     * @param  PreferenceFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getPreferences(PreferenceFilter $filters, ?int $ttl = null): Preference
    {
        $cacheKey = $this->getItemsCacheKey($this->TYPE, $filters);
        $tags = $this->getCacheTags($this->TYPE);

        return $this->cacheRemember($cacheKey, $tags, function () use ($filters) {
            // TODO:: remove firstOrFail when multiple preferences per user are supported
            return Preference::filter($filters)->firstOrFail();
        }, $ttl ?? $this->TTL);
    }

    /**
     * Create a new Preference
     */
    public function createPreference(array $data): Preference
    {
        $preference = Preference::create($data);

        return $preference;
    }

    /**
     * Get Preferences with filtering
     *
     * @param  string  $id  Preference ID
     * @param  PreferenceFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     * @return mixed
     */
    public function getPreference(string $id, PreferenceFilter $filters, ?int $ttl = null)
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id, $filters);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id, $filters) {
            return Preference::filter($filters)->findOrFail($id);
        }, $ttl ?? $this->TTL);
    }

    /**
     * Get Preference by ID without filtering
     *
     * @param  string  $id  Preference ID
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getPreferenceById(string $id, ?int $ttl = null): Preference
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id) {
            return Preference::findOrFail($id);
        }, $ttl ?? $this->TTL);
    }

    /**
     * Update Preference
     */
    public function updatePreference(Preference $preference, array $data): Preference
    {
        // I actually need to replace instead of merge here. The frontend sends the full properties object.
        //        $mergedProperties = array_merge($preference->properties ?? [], $data['properties'] ?? []);
        //        $data['properties'] = $mergedProperties;
        $preference->update($data);

        return $preference;
    }

    /**
     * Delete Preference
     */
    public function deletePreference(Preference $preference): void
    {
        $preference->delete();
    }
}
