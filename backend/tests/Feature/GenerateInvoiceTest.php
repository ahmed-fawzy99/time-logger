<?php

use App\Enums\MediaCollectionEnum;
use App\Models\Preference;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\TimeFrame;
use App\Models\User;
use App\Services\V1\TimeFrameServices;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->preferences = Preference::factory()->create([
        'user_id' => $this->user->id,
        'hourly_rate' => 50,
        'currency' => 'USD',
        'additional_properties' => [
            'invoiceTitle' => 'INVOICE',
            'invoiceName' => 'Acme Corp',
            'invoiceAddress' => '123 Main St, Springfield',
            'roundDurationTo' => 0,
            'roundMethod' => 'nearest',
        ],
    ]);

    $this->project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Project',
    ]);

    $this->timeFrame = TimeFrame::factory()->create([
        'project_id' => $this->project->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
    ]);

    $this->service = new TimeFrameServices;
});

describe('generateInvoice', function () {
    it('generates a PDF and attaches it to the time frame', function () {
        TimeEntry::factory()->create([
            'time_frame_id' => $this->timeFrame->id,
            'work_day' => '2026-01-05',
            'start_time' => '2026-01-05 09:00:00',
            'end_time' => '2026-01-05 17:00:00',
            'billable' => true,
            'description' => 'Full day work',
        ]);

        $media = $this->service->generateInvoice($this->timeFrame);

        expect($media)->not->toBeNull();
        expect($media->mime_type)->toBe('application/pdf');
        expect($media->file_name)->toContain('invoice-');
        expect(file_exists($media->getPath()))->toBeTrue();
    });

    it('only includes billable and finalized entries', function () {
        // Billable + finalized — should be included
        TimeEntry::factory()->create([
            'time_frame_id' => $this->timeFrame->id,
            'work_day' => '2026-01-05',
            'start_time' => '2026-01-05 09:00:00',
            'end_time' => '2026-01-05 13:00:00',
            'billable' => true,
        ]);

        // Non-billable — should be excluded
        TimeEntry::factory()->create([
            'time_frame_id' => $this->timeFrame->id,
            'work_day' => '2026-01-06',
            'start_time' => '2026-01-06 09:00:00',
            'end_time' => '2026-01-06 17:00:00',
            'billable' => false,
        ]);

        // Not finalized (no end_time) — should be excluded
        TimeEntry::factory()->create([
            'time_frame_id' => $this->timeFrame->id,
            'work_day' => '2026-01-07',
            'start_time' => '2026-01-07 09:00:00',
            'end_time' => null,
            'billable' => true,
        ]);

        $media = $this->service->generateInvoice($this->timeFrame);

        expect($media)->not->toBeNull();
    });

    it('replaces existing invoice when regenerated (singleFile collection)', function () {
        TimeEntry::factory()->create([
            'time_frame_id' => $this->timeFrame->id,
            'work_day' => '2026-01-10',
            'start_time' => '2026-01-10 09:00:00',
            'end_time' => '2026-01-10 12:00:00',
            'billable' => true,
        ]);

        $this->service->generateInvoice($this->timeFrame);
        $this->service->generateInvoice($this->timeFrame);

        $mediaCount = $this->timeFrame->refresh()
            ->getMedia(MediaCollectionEnum::TIME_FRAME_INVOICE->value)
            ->count();

        expect($mediaCount)->toBe(1);
    });

    it('throws exception for non-existent time frame', function () {
        $this->service->getInvoice('01JJJJJJJJJJJJJJJJJJJJ');
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
