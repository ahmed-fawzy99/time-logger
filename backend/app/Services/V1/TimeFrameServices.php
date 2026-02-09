<?php

declare(strict_types=1);

namespace App\Services\V1;

use App\Enums\CacheTagEnum;
use App\Enums\MediaCollectionEnum;
use App\Helpers\C;
use App\Http\Filters\Api\V1\Filters\TimeFrameFilter;
use App\Models\TimeFrame;
use App\Traits\Api\CacheRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TimeFrameServices
{
    use CacheRequest;

    private string $TYPE = CacheTagEnum::TIME_FRAME->value;

    private int $TTL = C::TWELVE_HOURS;

    /**
     * Get all Timeframes with pagination and filtering
     *
     * @param  TimeFrameFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getTimeframes(TimeFrameFilter $filters, ?int $ttl = null): mixed
    {
        $cacheKey = $this->getItemsCacheKey($this->TYPE, $filters);
        $tags = $this->getCacheTags($this->TYPE);

        return $this->cacheRemember($cacheKey, $tags, function () use ($filters) {
            return TimeFrame::filter($filters)->jsonPaginate();
        }, $ttl ?? $this->TTL);
    }

    /**
     * Create a new TimeFrame
     */
    public function createTimeframe(array $data): TimeFrame
    {
        $timeFrame = TimeFrame::create($data);

        return $timeFrame;
    }

    /**
     * Get Timeframes with filtering
     *
     * @param  string  $id  TimeFrame ID
     * @param  TimeFrameFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     * @return mixed
     */
    public function getTimeframe(string $id, TimeFrameFilter $filters, ?int $ttl = null)
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id, $filters);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id, $filters) {
            return TimeFrame::filter($filters)->findOrFail($id);
        }, $ttl ?? $this->TTL);
    }

    /**
     * Get TimeFrame by ID without filtering
     *
     * @param  string  $id  TimeFrame ID
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getTimeframeById(string $id, ?int $ttl = null): TimeFrame
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id) {
            return TimeFrame::findOrFail($id);
        }, $ttl ?? $this->TTL);
    }

    /**
     * Update TimeFrame
     */
    public function updateTimeframe(TimeFrame $timeFrame, array $data): TimeFrame
    {
        $timeFrame->update($data);

        return $timeFrame;
    }

    /**
     * Delete TimeFrame
     */
    public function deleteTimeframe(TimeFrame $timeFrame): void
    {
        $timeFrame->delete();
    }

    public function getInvoice(string $timeFrameId): string
    {
        $timeFrame = TimeFrame::with([
            'timeEntries' => fn ($q) => $q->billable()->finalized()->reorder('work_day', 'asc')->orderBy('start_time', 'asc'),
            'project',
            'user.preferences',
            'media',
        ])
            ->findOrFail($timeFrameId);

        return $this->generateInvoice($timeFrame)->getFullUrl();

    }

    /**
     * Generate an invoice PDF for billable entries and store it via Spatie Media Library.
     */
    public function generateInvoice(TimeFrame $timeFrame): Media
    {
        $hourlyRate = $timeFrame->hourly_rate;
        $currency = $timeFrame->currency;
        $preferences = $timeFrame->user->preferences;

        if (is_null($hourlyRate) || is_null($currency)) {
            $hourlyRate = $preferences->hourly_rate;
            $currency = $preferences->currency;
        }
        $additionalProperties = $preferences->additional_properties ?? [];

        $totalSeconds = 0;
        $totalAmount = 0;

        $entries = $timeFrame->timeEntries->map(function ($entry) use ($hourlyRate, &$totalSeconds, &$totalAmount) {
            $seconds = $entry->duration_in_seconds;
            $hours = $seconds / 3600;
            $amount = $hours * $hourlyRate;

            $totalSeconds += $seconds;
            $totalAmount += $amount;

            return [
                'work_day' => $entry->work_day->format('M d, Y'),
                'description' => $entry->description,
                'duration' => $this->formatDuration($seconds),
                'amount' => $amount,
            ];
        })->toArray();

        $pdf = Pdf::loadView('pdf.invoice', [
            'primaryColor' => $additionalProperties['invoicePrimaryColor'] ?? '#E05A2D',
            'invoiceTitle' => $additionalProperties['invoiceTitle'] ?? 'INVOICE',
            'invoiceName' => $additionalProperties['invoiceName'] ?? null,
            'invoiceAddress' => $additionalProperties['invoiceAddress'] ?? null,
            'projectName' => $timeFrame->project->name,
            'startDate' => $timeFrame->start_date->format('M d, Y'),
            'endDate' => $timeFrame->end_date->format('M d, Y'),
            'currency' => $currency,
            'hourlyRate' => $hourlyRate,
            'entries' => $entries,
            'totalDuration' => $this->formatDuration($totalSeconds),
            'totalAmount' => $totalAmount,
            'generatedAt' => now()->format('M d, Y \a\t h:i A'),
        ])->setPaper('a4');

        $fileName = 'invoice-'.str($timeFrame->project->name)->slug().'-'.$timeFrame->start_date->format('Y-m-d').'.pdf';
        $tempPath = storage_path('app/private/'.$fileName);

        $pdf->save($tempPath);

        $media = $timeFrame->addMedia($tempPath)
            ->usingFileName($fileName)
            ->toMediaCollection(MediaCollectionEnum::TIME_FRAME_INVOICE->value);

        \Cache::tags([CacheTagEnum::TIME_FRAME->value])->flush();

        return $media;
    }

    /**
     * Format seconds into a human-readable duration string (e.g. "2h 30m").
     */
    private function formatDuration(int|float $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        }

        if ($hours > 0) {
            return "{$hours}h 0m";
        }

        return "{$minutes}m";
    }
}
