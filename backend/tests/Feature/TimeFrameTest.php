<?php

use App\Enums\TimeFrameStatusEnum;
use App\Models\Preference;
use App\Models\Project;
use App\Models\TimeFrame;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->baseUrl = '/api/v1/time-frames';
    if (Preference::count() === 0) {
        Preference::factory()->create();
    }

    Cache::tags(\App\Enums\CacheTagEnum::TIME_FRAME->value)->flush();
});

describe('GET /api/v1/time-frames (index)', function () {
    it('returns paginated list of timeFrames', function () {
        TimeFrame::factory()->count(5)->create();

        $response = getJson($this->baseUrl);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'attributes' => [
                            'startDate',
                            'endDate',
                            'name',
                            'status',
                            'notes',
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

    it('returns empty list when no timeFrames exist', function () {
        $response = getJson($this->baseUrl);

        $response->assertSuccessful()
            ->assertJsonCount(0, 'data');
    });

    it('supports pagination with page number', function () {
        TimeFrame::factory()->count(30)->create();

        $response = getJson("{$this->baseUrl}?page[number]=2&page[size]=10");

        $response->assertSuccessful()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 2);
    });

    it('supports custom page size', function () {
        TimeFrame::factory()->count(15)->create();

        $response = getJson("{$this->baseUrl}?page[size]=5");

        $response->assertSuccessful()
            ->assertJsonCount(5, 'data');
    });

    it('handles server errors gracefully', function () {
        $this->mock(\App\Services\V1\TimeFrameServices::class)
            ->shouldReceive('getTimeframes')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = getJson($this->baseUrl);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Timeframes Retrieval Error',
            ]);
    });
});

describe('GET /api/v1/time-frames/{id} (show)', function () {
    it('returns a single timeFrame', function () {
        $timeFrame = TimeFrame::factory()->create([
            'name' => 'Q1 2024',
            'status' => TimeFrameStatusEnum::IN_PROGRESS,
        ]);

        $response = getJson("{$this->baseUrl}/{$timeFrame->id}");

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'TimeFrame Retrieved Successfully',
                'data' => [
                    'id' => $timeFrame->id,
                    'type' => 'timeFrame',
                    'attributes' => [
                        'name' => 'Q1 2024',
                        'status' => 'in_progress',
                    ],
                ],
            ]);
    });

    it('returns 404 for non-existent timeFrame', function () {
        $response = getJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ");

        $response->assertNotFound()
            ->assertJson([
                'message' => 'TimeFrame not found',
            ]);
    });

    it('handles server errors gracefully', function () {
        $timeFrame = TimeFrame::factory()->create();

        $this->mock(\App\Services\V1\TimeFrameServices::class)
            ->shouldReceive('getTimeframe')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = getJson("{$this->baseUrl}/{$timeFrame->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'TimeFrame Error',
            ]);
    });
});

describe('POST /api/v1/time-frames (store)', function () {
    it('creates a new timeFrame with required fields', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJson([
                'message' => 'TimeFrame Created',
                'data' => [
                    'type' => 'timeFrame',
                    'attributes' => [
                        'startDate' => '2024-01-01T00:00:00.000000Z',
                        'endDate' => '2024-03-31T00:00:00.000000Z',
                    ],
                ],
            ]);

        $timeFrame = TimeFrame::latest()->first();
        expect($timeFrame->start_date->toDateString())->toBe('2024-01-01');
        expect($timeFrame->end_date->toDateString())->toBe('2024-03-31');
    });

    it('creates a timeFrame with all optional fields', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'name' => 'Q1 2024 Sprint',
            'status' => TimeFrameStatusEnum::IN_PROGRESS->value,
            'notes' => 'This is a test timeFrame',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJson([
                'message' => 'TimeFrame Created',
                'data' => [
                    'type' => 'timeFrame',
                    'attributes' => [
                        'name' => 'Q1 2024 Sprint',
                        'status' => 'in_progress',
                        'notes' => 'This is a test timeFrame',
                    ],
                ],
            ]);
    });

    it('validates required start_date field', function () {
        $data = [
            'end_date' => '2024-03-31',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['start_date']);
    });

    it('validates required end_date field', function () {
        $data = [
            'start_date' => '2024-01-01',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    });

    it('validates start_date is a valid date', function () {
        $data = [
            'start_date' => 'invalid-date',
            'end_date' => '2024-03-31',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['start_date']);
    });

    it('validates project_id is a valid', function () {
        $data = [
            'start_date' => '2024-03-30',
            'end_date' => '2024-03-31',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['project_id']);

        $data = [
            'project_id' => 'invalid-id',
            'start_date' => '2024-03-30',
            'end_date' => '2024-03-31',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['project_id']);
    });

    it('validates end_date is a valid date', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => 'invalid-date',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    });

    it('validates end_date is after or equal to start_date', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-03-31',
            'end_date' => '2024-01-01',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    });

    it('allows end_date to equal start_date', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-01',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated();
    });

    it('validates name must be string when provided', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'name' => 12345,
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates name max length is 255 characters', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'name' => str_repeat('a', 256),
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates status must be a valid enum value', function () {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'status' => 'invalid_status',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    });

    it('accepts valid status values', function (string $status) {
        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'status' => $status,
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJsonPath('data.attributes.status', $status);
    })->with([
        'done' => TimeFrameStatusEnum::DONE->value,
        'in_progress' => TimeFrameStatusEnum::IN_PROGRESS->value,
    ]);

    it('validates notes must be string when provided', function () {
        $data = [
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'notes' => ['array', 'not', 'allowed'],
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['notes']);
    });

    it('handles server errors gracefully', function () {
        $this->mock(\App\Services\V1\TimeFrameServices::class)
            ->shouldReceive('createTimeframe')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $data = [
            'project_id' => Project::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'TimeFrame Creation Error',
            ]);
    });
});

describe('PUT /api/v1/time-frames/{id} (update)', function () {
    it('updates an existing timeFrame', function () {
        $timeFrame = TimeFrame::factory()->create([
            'name' => 'Old Name',
            'status' => TimeFrameStatusEnum::IN_PROGRESS,
        ]);

        $data = [
            'start_date' => '2024-04-01',
            'end_date' => '2024-06-30',
            'name' => 'Updated Name',
            'status' => TimeFrameStatusEnum::DONE->value,
        ];

        $response = putJson("{$this->baseUrl}/{$timeFrame->id}", $data);

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'TimeFrame Updated',
                'data' => [
                    'id' => $timeFrame->id,
                    'type' => 'timeFrame',
                    'attributes' => [
                        'name' => 'Updated Name',
                        'status' => 'done',
                    ],
                ],
            ]);

        $timeFrame->refresh();
        expect($timeFrame->name)->toBe('Updated Name');
        expect($timeFrame->status)->toBe(TimeFrameStatusEnum::DONE);
        expect($timeFrame->start_date->toDateString())->toBe('2024-04-01');
        expect($timeFrame->end_date->toDateString())->toBe('2024-06-30');
    });

    it('updates only specific fields', function () {
        $timeFrame = TimeFrame::factory()->create([
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'name' => 'Original Name',
        ]);

        $data = [
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
            'name' => 'Updated Name Only',
        ];

        $response = putJson("{$this->baseUrl}/{$timeFrame->id}", $data);

        $response->assertSuccessful()
            ->assertJsonPath('data.attributes.name', 'Updated Name Only');
    });

    it('returns 404 for non-existent timeFrame', function () {
        $data = [
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
        ];

        $response = putJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ", $data);

        $response->assertNotFound()
            ->assertJson([
                'message' => 'TimeFrame not found',
            ]);
    });

    it('validates all required fields on update', function () {
        $timeFrame = TimeFrame::factory()->create();

        $response = putJson("{$this->baseUrl}/{$timeFrame->id}", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['start_date', 'end_date']);
    });

    it('validates end_date is after or equal to start_date on update', function () {
        $timeFrame = TimeFrame::factory()->create();

        $data = [
            'start_date' => '2024-12-31',
            'end_date' => '2024-01-01',
        ];

        $response = putJson("{$this->baseUrl}/{$timeFrame->id}", $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    });

    it('handles server errors gracefully', function () {
        $timeFrame = TimeFrame::factory()->create();

        $this->mock(\App\Services\V1\TimeFrameServices::class)
            ->shouldReceive('getTimeframeById')
            ->once()
            ->andReturn($timeFrame)
            ->shouldReceive('updateTimeframe')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $data = [
            'start_date' => '2024-01-01',
            'end_date' => '2024-03-31',
        ];

        $response = putJson("{$this->baseUrl}/{$timeFrame->id}", $data);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'TimeFrame Update Error',
            ]);
    });
});

describe('DELETE /api/v1/time-frames/{id} (destroy)', function () {
    it('deletes an existing timeFrame', function () {
        $timeFrame = TimeFrame::factory()->create();
        $timeFrameId = $timeFrame->id;

        $response = deleteJson("{$this->baseUrl}/{$timeFrameId}");

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'TimeFrame deleted successfully',
            ]);

        expect(TimeFrame::find($timeFrameId))->toBeNull();
    });

    it('returns 404 when deleting non-existent timeFrame', function () {
        $response = deleteJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ");

        $response->assertNotFound()
            ->assertJson([
                'message' => 'TimeFrame not found',
            ]);
    });

    it('handles server errors gracefully', function () {
        $timeFrame = TimeFrame::factory()->create();

        $this->mock(\App\Services\V1\TimeFrameServices::class)
            ->shouldReceive('getTimeframeById')
            ->once()
            ->andReturn($timeFrame)
            ->shouldReceive('deleteTimeframe')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = deleteJson("{$this->baseUrl}/{$timeFrame->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'TimeFrame deletion failed',
            ]);
    });
});
