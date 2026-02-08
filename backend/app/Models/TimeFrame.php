<?php

namespace App\Models;

use App\Enums\CacheTagEnum;
use App\Enums\MediaCollectionEnum;
use App\Enums\TimeFrameStatusEnum;
use App\Helpers\C;
use App\Http\Filters\Api\V1\Filters\TimeFrameFilter;
use App\Traits\Api\HasCacheControl;
use Database\Factories\TimeFrameFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TimeFrame extends Model implements HasMedia
{
    /** @use HasFactory<TimeFrameFactory> */
    use HasCacheControl, HasFactory, HasUlids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'project_id',
        'start_date',
        'end_date',
        'name',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => TimeFrameStatusEnum::class,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionEnum::TIME_FRAME_INVOICE->value)->singleFile();
    }

    /**
     * Get the time entries for the timeframe
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class)->orderBy('work_day', 'desc')->orderBy('start_time', 'desc');
    }

    /**
     * Get the project that owns the timeframe
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user that owns the timeframe (project.user)
     */
    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Project::class,
            'id',
            'id',
            'project_id',
            'user_id',
        );
    }

    /**
     * Apply filter to query
     */
    public function scopeFilter(Builder $query, TimeFrameFilter $filters): void
    {
        $filters->apply($query);
    }

    /**
     * Apply filter to query
     */
    public function scopeWithTotalBillableSeconds(Builder $query): void
    {
        $query->withSum(
            ['timeEntries as total_billable_seconds' => fn ($q) => $q->billable()->finalized()],
            \DB::raw('EXTRACT(EPOCH FROM (end_time - start_time))')
        );
    }

    protected function periodDurationInDays(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_date->diffInDays($this->end_date, true),
        );
    }

    protected function totalRecordedDurationInMinutes(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->relationLoaded('timeEntries')) {
                    return null;
                }

                return $this->timeEntries->whereNotNull('end_time')->sum(function (TimeEntry $entry) {
                    return $entry->start_time->diffInMinutes($entry->end_time, true);
                });
            },
        );
    }

    protected function entriesCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->relationLoaded('timeEntries')) {
                    return null;
                }

                return $this->entries_count ?? $this->timeEntries->count();
            },
        );
    }

    protected function averageDailyDurationInMinutes(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->relationLoaded('timeEntries')) {
                    return null;
                }

                return $this->timeEntries->whereNotNull('end_time')->avg(function (TimeEntry $entry) {
                    return $entry->start_time->diffInMinutes($entry->end_time, true);
                });
            },
        );
    }

    protected function totalBillable(): Attribute
    {
        return Attribute::make(
            get: function () {
                $preferences = \Cache::tags([CacheTagEnum::PREFERENCE->value])
                    ->remember('global_preferences',
                        C::ONE_DAY,
                        function () {
                            return Preference::first();
                        });

                $amt = ($this->total_billable_seconds / 3600) * $preferences->hourly_rate;

                return $preferences->currency.' '.number_format($amt, 2);
            },
        );
    }

    protected function daysTracked(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->relationLoaded('timeEntries')) {
                    return null;
                }

                return $this->timeEntries->pluck('work_day')->unique()->count();
            },
        );
    }

    protected function invoiceUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->relationLoaded('media') || $this->status !== TimeFrameStatusEnum::DONE) {
                    return null;
                }

                return $this->getFirstMedia(MediaCollectionEnum::TIME_FRAME_INVOICE->value)?->getFullUrl();
            },
        );
    }

    /**
     * Get mutators for API
     *
     * Grouped and cached because they may involve some calculations and additional queries
     */
    public function mutators(): Attribute
    {
        return Attribute::make(
            fn () => \Cache::tags([CacheTagEnum::TIME_FRAME->value, CacheTagEnum::TIME_ENTRY->value])->remember(
                "{$this->id}_mutators",
                C::TWELVE_HOURS,
                fn () => [
                    'periodDurationInDays' => $this->period_duration_in_days,
                    'totalRecordedDurationInMinutes' => $this->total_recorded_duration_in_minutes,
                    'entriesCount' => $this->entries_count,
                    'averageDailyDurationInMinutes' => $this->average_daily_duration_in_minutes,
                    'totalBillable' => $this->total_billable,
                    'daysTracked' => $this->days_tracked,
                ]
            )
        );
    }
}
