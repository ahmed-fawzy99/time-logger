<?php

use App\Models\Preference;
use App\Models\Project;
use App\Models\TimeFrame;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->baseUrl = '/api/v1/projects';
    if (Preference::count() === 0) {
        Preference::factory()->create();
    }

    Cache::tags(\App\Enums\CacheTagEnum::PROJECT->value)->flush();
});

describe('GET /api/v1/projects (index)', function () {
    it('returns paginated list of projects', function () {
        Project::factory()->count(5)->create();

        $response = getJson($this->baseUrl);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'attributes' => [
                            'name',
                            'slug',
                            'description',
                            'additionalProperties',
                        ],
                        'links' => ['self'],
                        'relationships',
                        'includes',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(5, 'data');
    });

    it('returns empty list when no projects exist', function () {
        $response = getJson($this->baseUrl);

        $response->assertSuccessful()
            ->assertJsonCount(0, 'data');
    });

    it('supports pagination with page number', function () {
        Project::factory()->count(30)->create();

        $response = getJson("{$this->baseUrl}?page[number]=2&page[size]=10");

        $response->assertSuccessful()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 2);
    });

    it('supports custom page size', function () {
        Project::factory()->count(15)->create();

        $response = getJson("{$this->baseUrl}?page[size]=5");

        $response->assertSuccessful()
            ->assertJsonCount(5, 'data');
    });

    it('handles server errors gracefully', function () {
        $this->mock(\App\Services\V1\ProjectServices::class)
            ->shouldReceive('getProjects')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = getJson($this->baseUrl);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Projects Retrieval Error',
            ]);
    });
});

describe('GET /api/v1/projects/{id} (show)', function () {
    it('returns a single project by id', function () {
        $project = Project::factory()->create([
            'name' => 'Test Project',
            'description' => 'A test project description',
        ]);

        $response = getJson("{$this->baseUrl}/{$project->id}");

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'Project Retrieved Successfully',
                'data' => [
                    'id' => $project->id,
                    'type' => 'project',
                    'attributes' => [
                        'name' => 'Test Project',
                        'description' => 'A test project description',
                    ],
                ],
            ]);
    });

    it('returns a single project by slug', function () {
        $project = Project::factory()->create([
            'name' => 'My Slug Project',
        ]);

        $response = getJson("{$this->baseUrl}/{$project->slug}");

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'Project Retrieved Successfully',
                'data' => [
                    'id' => $project->id,
                    'type' => 'project',
                ],
            ]);
    });

    it('returns 404 for non-existent project', function () {
        $response = getJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ");

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Project not found',
            ]);
    });

    it('handles server errors gracefully', function () {
        $project = Project::factory()->create();

        $this->mock(\App\Services\V1\ProjectServices::class)
            ->shouldReceive('getProject')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = getJson("{$this->baseUrl}/{$project->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Project Error',
            ]);
    });
});

describe('POST /api/v1/projects (store)', function () {
    it('creates a new project with required fields', function () {
        $data = [
            'name' => 'New Project',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJson([
                'message' => 'Project Created',
                'data' => [
                    'type' => 'project',
                    'attributes' => [
                        'name' => 'New Project',
                    ],
                ],
            ]);

        $project = Project::latest()->first();
        expect($project->name)->toBe('New Project');
        expect($project->slug)->not->toBeNull();
    });

    it('creates a project with all fields', function () {
        $data = [
            'name' => 'Full Project',
            'description' => 'A complete project description',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJson([
                'message' => 'Project Created',
                'data' => [
                    'type' => 'project',
                    'attributes' => [
                        'name' => 'Full Project',
                        'description' => 'A complete project description',
                    ],
                ],
            ]);
    });

    it('auto-generates a slug from the name', function () {
        $data = [
            'name' => 'My Amazing Project',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated();

        $project = Project::latest()->first();
        expect($project->slug)->toBe('my-amazing-project');
    });

    it('validates required name field', function () {
        $response = postJson($this->baseUrl, []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates name must be a string', function () {
        $data = [
            'name' => 12345,
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates name max length is 255 characters', function () {
        $data = [
            'name' => str_repeat('a', 256),
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates description must be a string when provided', function () {
        $data = [
            'name' => 'Valid Name',
            'description' => ['array', 'not', 'allowed'],
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['description']);
    });

    it('allows null description', function () {
        $data = [
            'name' => 'Project Without Description',
            'description' => null,
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertCreated();
    });

    it('handles server errors gracefully', function () {
        $this->mock(\App\Services\V1\ProjectServices::class)
            ->shouldReceive('createProject')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $data = [
            'name' => 'New Project',
        ];

        $response = postJson($this->baseUrl, $data);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Project Creation Error',
            ]);
    });
});

describe('PUT /api/v1/projects/{id} (update)', function () {
    it('updates an existing project', function () {
        $project = Project::factory()->create([
            'name' => 'Old Name',
            'description' => 'Old description',
        ]);

        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = putJson("{$this->baseUrl}/{$project->id}", $data);

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'Project Updated',
                'data' => [
                    'id' => $project->id,
                    'type' => 'project',
                    'attributes' => [
                        'name' => 'Updated Name',
                        'description' => 'Updated description',
                    ],
                ],
            ]);

        $project->refresh();
        expect($project->name)->toBe('Updated Name');
        expect($project->description)->toBe('Updated description');
    });

    it('returns 404 for non-existent project', function () {
        $data = [
            'name' => 'Updated Name',
        ];

        $response = putJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ", $data);

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Project not found',
            ]);
    });

    it('validates required fields on update', function () {
        $project = Project::factory()->create();

        $response = putJson("{$this->baseUrl}/{$project->id}", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates name max length on update', function () {
        $project = Project::factory()->create();

        $data = [
            'name' => str_repeat('a', 256),
        ];

        $response = putJson("{$this->baseUrl}/{$project->id}", $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('handles server errors gracefully', function () {
        $project = Project::factory()->create();

        $this->mock(\App\Services\V1\ProjectServices::class)
            ->shouldReceive('getProjectById')
            ->once()
            ->andReturn($project)
            ->shouldReceive('updateProject')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $data = [
            'name' => 'Updated Name',
        ];

        $response = putJson("{$this->baseUrl}/{$project->id}", $data);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Project Update Error',
            ]);
    });
});

describe('DELETE /api/v1/projects/{id} (destroy)', function () {
    it('deletes an existing project', function () {
        $project = Project::factory()->create();
        $projectId = $project->id;

        $response = deleteJson("{$this->baseUrl}/{$projectId}");

        $response->assertSuccessful()
            ->assertJson([
                'message' => 'Project deleted successfully',
            ]);

        expect(Project::find($projectId))->toBeNull();
    });

    it('soft deletes the project', function () {
        $project = Project::factory()->create();
        $projectId = $project->id;

        deleteJson("{$this->baseUrl}/{$projectId}");

        expect(Project::find($projectId))->toBeNull();
        expect(Project::withTrashed()->find($projectId))->not->toBeNull();
    });

    it('returns 404 when deleting non-existent project', function () {
        $response = deleteJson("{$this->baseUrl}/01JJJJJJJJJJJJJJJJJJJJ");

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Project not found',
            ]);
    });

    it('handles server errors gracefully', function () {
        $project = Project::factory()->create();

        $this->mock(\App\Services\V1\ProjectServices::class)
            ->shouldReceive('getProjectById')
            ->once()
            ->andReturn($project)
            ->shouldReceive('deleteProject')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = deleteJson("{$this->baseUrl}/{$project->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Project deletion failed',
            ]);
    });
});

describe('Project Model', function () {
    it('belongs to a user', function () {
        $project = Project::factory()->create();

        expect($project->user)->not->toBeNull();
        expect($project->user)->toBeInstanceOf(\App\Models\User::class);
    });

    it('has many time frames', function () {
        $project = Project::factory()->create();
        TimeFrame::factory()->count(3)->create(['project_id' => $project->id]);

        expect($project->timeFrames)->toHaveCount(3);
    });

    it('has many time entries through time frames', function () {
        $project = Project::factory()->create();
        $timeFrame = TimeFrame::factory()->create(['project_id' => $project->id]);
        \App\Models\TimeEntry::factory()->count(2)->create(['time_frame_id' => $timeFrame->id]);

        expect($project->timeEntries)->toHaveCount(2);
    });

    it('uses soft deletes', function () {
        $project = Project::factory()->create();
        $project->delete();

        expect(Project::find($project->id))->toBeNull();
        expect(Project::withTrashed()->find($project->id))->not->toBeNull();
    });

    it('casts additional_properties to array', function () {
        $project = Project::factory()->create([
            'additional_properties' => ['key' => 'value'],
        ]);

        $project->refresh();
        expect($project->additional_properties)->toBeArray();
        expect($project->additional_properties)->toBe(['key' => 'value']);
    });

    it('generates slug from name via sluggable', function () {
        $project = Project::factory()->create([
            'name' => 'My Test Project',
            'slug' => null,
        ]);

        $project->refresh();
        expect($project->slug)->toBe('my-test-project');
    });
});
