<?php

use App\Models\TimeEntry;
use App\Models\TimeFrame;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->baseUrl = '/api/v1/time-entries';

    Cache::tags(\App\Enums\CacheTagEnum::TIME_ENTRY->value)->flush();
});

describe('GET /api/v1/time-entries (index)', function () {
    it('returns paginated list of time entries with correct structure', function () {
        TimeEntry::factory()->count(5)->create();

        $response = getJson($this->baseUrl);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'attributes' => [
                            'workDay',
                            'startTime',
                            'endTime',
                            'description',
                            'billable',
                            'createdAt',
                            'updatedAt',
                        ],
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(5, 'data');
    });

    it('returns empty list when no time entries exist', function () {
        $response = getJson($this->baseUrl);

        $response->assertSuccessful()
            ->assertJsonCount(0, 'data');
    });

    it('supports pagination with page number', function () {
        TimeEntry::factory()->count(30)->create();

        $response = getJson("{$this->baseUrl}?page[number]=2&page[size]=10");

        $response->assertSuccessful()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 2);
    });

    it('supports custom page size', function () {
        TimeEntry::factory()->count(15)->create();

        $response = getJson("{$this->baseUrl}?page[size]=5");

        $response->assertSuccessful()
            ->assertJsonCount(5, 'data');
    });
});

describe('GET /api/v1/time-entries/{id} (show)', function () {
    it('returns a single time entry', function () {
        $timeFrame = TimeFrame::factory()->create();
        $timeEntry = TimeEntry::factory()->create([
            'time_frame_id' => $timeFrame->id,
            'description' => 'Working on feature',
            'billable' => true,
        ]);

        $response = getJson("{$this->baseUrl}/{$timeEntry->id}");

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'Time Entry Retrieved Successfully',
                'data' => [
                    'id' => $timeEntry->id,
                    'type' => 'timeEntry',
                    'attributes' => [
                        'description' => 'Working on feature',
                        'billable' => true,
                    ],
                ],
            ]);
    });

    it('returns 404 for non-existent time entry', function () {
        $response = getJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ");

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Time Entry not found',
            ]);
    });

    it('handles server errors gracefully', function () {
        $timeEntry = TimeEntry::factory()->create();

        $this->mock(\App\Services\V1\TimeEntryServices::class)
            ->shouldReceive('getTimeEntry')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = getJson("{$this->baseUrl}/{$timeEntry->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Time Entry Error',
            ]);
    });
});

describe('POST /api/v1/time-entries (store)', function () {
    it('creates a time entry with required fields only', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'end_time' => '2024-02-15 09:00:01',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJson([
                'message' => 'Time Entry Created',
                'data' => [
                    'type' => 'timeEntry',
                    'attributes' => [
                        'workDay' => '2024-02-15T00:00:00.000000Z',
                    ],
                ],
            ]);

        $timeEntry = TimeEntry::latest()->first();
        expect($timeEntry->time_frame_id)->toBe($timeFrame->id);
        expect($timeEntry->work_day->toDateString())->toBe('2024-02-15');
    });

    it('creates a time entry with all optional fields', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'end_time' => '2024-02-15 17:00:00',
            'description' => 'Working on important feature',
            'billable' => true,
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJson([
                'message' => 'Time Entry Created',
                'data' => [
                    'type' => 'timeEntry',
                    'attributes' => [
                        'description' => 'Working on important feature',
                        'billable' => true,
                    ],
                ],
            ]);
    });

    it('validates required time_frame_id field', function () {
        $data = [
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['time_frame_id']);
    });

    it('validates time_frame_id exists in database', function () {
        $data = [
            'time_frame_id' => '01JJJJJJJJJJJJJJJJJJJJ',
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['time_frame_id']);
    });

    it('validates required start_time field', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['start_time']);
    });

    it('validates start_time is a valid date', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => 'invalid-time',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['start_time']);
    });

    it('validates end_time is after start_time', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 17:00:00',
            'end_time' => '2024-02-15 09:00:00',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_time']);
    });

    it('refuses null end_time', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'end_time' => null,
        ];

        $response = postJson($this->baseUrl, $data);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_time']);
    });

    it('validates description must be string', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'description' => ['array', 'not', 'allowed'],
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['description']);
    });

    it('validates billable must be boolean', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'billable' => 'not-a-boolean',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['billable']);
    });

    it('handles server errors gracefully', function () {
        $this->mock(\App\Services\V1\TimeEntryServices::class)
            ->shouldReceive('createTimeEntry')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'end_time' => '2024-02-15 17:00:00',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Time Entry Creation Error',
            ]);
    });
});

describe('PUT /api/v1/time-entries/{id} (update)', function () {
    it('updates an existing time entry', function () {
        $timeFrame = TimeFrame::factory()->create();
        $timeEntry = TimeEntry::factory()->create([
            'time_frame_id' => $timeFrame->id,
            'description' => 'Old description',
            'billable' => false,
        ]);

        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-03-01',
            'start_time' => '2024-03-01 10:00:00',
            'end_time' => '2024-03-01 18:00:00',
            'description' => 'Updated description',
            'billable' => true,
        ];

        $response = putJson("{$this->baseUrl}/{$timeEntry->id}", $data);

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'Time Entry Updated',
                'data' => [
                    'id' => $timeEntry->id,
                    'type' => 'timeEntry',
                    'attributes' => [
                        'description' => 'Updated description',
                        'billable' => true,
                    ],
                ],
            ]);

        $timeEntry->refresh();
        expect($timeEntry->description)->toBe('Updated description');
        expect($timeEntry->billable)->toBe(true);
    });

    it('returns 404 for non-existent time entry', function () {
        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'end_time' => '2024-02-15 17:00:00',
        ];

        $response = putJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ", $data);

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Time Entry not found',
            ]);
    });

    it('validates all required fields on update', function () {
        $timeEntry = TimeEntry::factory()->create();

        $response = putJson("{$this->baseUrl}/{$timeEntry->id}", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['time_frame_id', 'start_time', 'end_time']);
    });

    it('validates end_time is after start_time on update', function () {
        $timeFrame = TimeFrame::factory()->create();
        $timeEntry = TimeEntry::factory()->create();

        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 18:00:00',
            'end_time' => '2024-02-15 09:00:00',
        ];

        $response = putJson("{$this->baseUrl}/{$timeEntry->id}", $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_time']);
    });

    it('handles server errors gracefully', function () {
        $timeEntry = TimeEntry::factory()->create();

        $this->mock(\App\Services\V1\TimeEntryServices::class)
            ->shouldReceive('getTimeEntryById')
            ->once()
            ->andReturn($timeEntry)
            ->shouldReceive('updateTimeEntry')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $timeFrame = TimeFrame::factory()->create();
        $data = [
            'time_frame_id' => $timeFrame->id,
            'work_day' => '2024-02-15',
            'start_time' => '2024-02-15 09:00:00',
            'end_time' => '2024-02-15 17:00:00',
        ];

        $response = putJson("{$this->baseUrl}/{$timeEntry->id}", $data);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Time Entry Update Error',
            ]);
    });
});

describe('DELETE /api/v1/time-entries/{id} (destroy)', function () {
    it('deletes an existing time entry', function () {
        $timeEntry = TimeEntry::factory()->create();
        $timeEntryId = $timeEntry->id;

        $response = deleteJson("{$this->baseUrl}/{$timeEntryId}");

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'Time Entry deleted successfully',
            ]);

        expect(TimeEntry::find($timeEntryId))->toBeNull();
    });

    it('returns 404 when deleting non-existent time entry', function () {
        $response = deleteJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ");

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Time Entry not found',
            ]);
    });

    it('handles server errors gracefully', function () {
        $timeEntry = TimeEntry::factory()->create();

        $this->mock(\App\Services\V1\TimeEntryServices::class)
            ->shouldReceive('getTimeEntryById')
            ->once()
            ->andReturn($timeEntry)
            ->shouldReceive('deleteTimeEntry')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = deleteJson("{$this->baseUrl}/{$timeEntry->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Time Entry deletion failed',
            ]);
    });
});
