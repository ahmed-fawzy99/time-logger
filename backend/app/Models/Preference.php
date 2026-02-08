<?php

namespace App\Models;

use App\Http\Filters\Api\V1\Filters\PreferenceFilter;
use App\Traits\Api\HasCacheControl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    /** @use HasFactory<\Database\Factories\PreferenceFactory> */
    use HasCacheControl, HasFactory, HasUlids;

    protected $fillable = [
        'additional_properties',
        'hourly_rate',
        'currency',
        'week_start',
    ];

    protected $casts = [
        'additional_properties' => 'array',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Apply filter to query
     */
    public function scopeFilter(Builder $query, PreferenceFilter $filters): void
    {
        $filters->apply($query);
    }

    protected function hourlyRate(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value / 100, // Convert cents to dollars
            set: fn ($value) => $value * 100, // Convert dollars to cents
        );
    }
}
