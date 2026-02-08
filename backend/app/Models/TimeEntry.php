<?php

namespace App\Models;

use App\Http\Filters\Api\V1\Filters\TimeEntryFilter;
use App\Traits\Api\HasCacheControl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    /** @use HasFactory<\Database\Factories\TimeEntryFactory> */
    use HasCacheControl, HasFactory, HasUlids;

    protected $fillable = [
        'time_frame_id',
        'work_day',
        'start_time',
        'end_time',
        'description',
        'billable',
    ];

    protected $casts = [
        'work_day' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'billable' => 'boolean',
    ];

    /**
     * Get the timeframe that owns the time entry
     */
    public function timeFrame(): BelongsTo
    {
        return $this->belongsTo(TimeFrame::class);
    }

    /**
     * Get the user that owns the time entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Apply filter to query
     */
    public function scopeFilter(Builder $query, TimeEntryFilter $filters): void
    {
        $filters->apply($query);
    }

    public function scopeBillable(Builder $query): void
    {
        $query->where('billable', true);
    }

    // Where both start_time and end_time are not null
    public function scopeFinalized(Builder $query): void
    {
        $query->whereNotNull('start_time')
            ->whereNotNull('end_time');
    }

    protected function durationInSeconds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_time->diffInSeconds($this->end_time, true),
        );
    }
}
