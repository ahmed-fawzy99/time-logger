<?php

declare(strict_types=1);

namespace App\Services\V1;

namespace App\Services\V1;

use App\Enums\CacheTagEnum;
use App\Helpers\C;
use App\Http\Filters\Api\V1\Filters\TimeEntryFilter;
use App\Models\TimeEntry;
use App\Traits\Api\CacheRequest;
use Carbon\Carbon;

class TimeEntryServices
{
    use CacheRequest;

    private string $TYPE = CacheTagEnum::TIME_ENTRY->value;

    private int $TTL = C::TWELVE_HOURS;

    /**
     * Get all Time Entries with pagination and filtering
     *
     * @param  TimeEntryFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getTimeEntries(TimeEntryFilter $filters, ?int $ttl = null): mixed
    {
        $cacheKey = $this->getItemsCacheKey($this->TYPE, $filters);
        $tags = $this->getCacheTags($this->TYPE);

        return $this->cacheRemember($cacheKey, $tags, function () use ($filters) {
            return TimeEntry::filter($filters)->jsonPaginate();
        }, $ttl ?? $this->TTL);
    }

    /**
     * Create a new Time Entry
     */
    public function createTimeEntry(array $data): TimeEntry
    {
        $data['work_day'] = Carbon::parse($data['start_time'])->toDateString();
        $data['start_time'] = Carbon::parse($data['start_time'])->utc();
        $data['end_time'] = Carbon::parse($data['end_time'])->utc();
        $timeEntry = TimeEntry::create($data);

        return $timeEntry;
    }

    /**
     * Get Time Entry with filtering
     *
     * @param  string  $id  Time Entry ID
     * @param  TimeEntryFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     * @return mixed
     */
    public function getTimeEntry(string $id, TimeEntryFilter $filters, ?int $ttl = null)
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id, $filters);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id, $filters) {
            return TimeEntry::filter($filters)->findOrFail($id);
        }, $ttl ?? $this->TTL);
    }

    /**
     * Get Time Entry by ID without filtering
     *
     * @param  string  $id  Time Entry ID
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getTimeEntryById(string $id, ?int $ttl = null): TimeEntry
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id) {
            return TimeEntry::findOrFail($id);
        }, $ttl ?? $this->TTL);
    }

    /**
     * Update Time Entry
     */
    public function updateTimeEntry(TimeEntry $timeEntry, array $data): TimeEntry
    {
        $data['start_time'] = Carbon::parse($data['start_time'])->utc();
        $data['end_time'] = Carbon::parse($data['end_time'])->utc();
        $timeEntry->update($data);

        return $timeEntry;
    }

    /**
     * Delete Time Entry
     */
    public function deleteTimeEntry(TimeEntry $timeEntry): void
    {
        $timeEntry->delete();
    }
}
