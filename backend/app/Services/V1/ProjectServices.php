<?php

declare(strict_types=1);

namespace App\Services\V1;

namespace App\Services\V1;

use App\Enums\CacheTagEnum;
use App\Helpers\C;
use App\Http\Filters\Api\V1\Filters\ProjectFilter;
use App\Models\Project;
use App\Models\User;
use App\Traits\Api\CacheRequest;

class ProjectServices
{
    use CacheRequest;

    private string $TYPE = CacheTagEnum::PROJECT->value;

    private int $TTL = C::TWELVE_HOURS;

    /**
     * Get all Projects with pagination and filtering
     *
     * @param  ProjectFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getProjects(ProjectFilter $filters, ?int $ttl = null): mixed
    {
        $cacheKey = $this->getItemsCacheKey($this->TYPE, $filters);
        $tags = $this->getCacheTags($this->TYPE);

        return $this->cacheRemember($cacheKey, $tags, function () use ($filters) {
            return Project::filter($filters)->jsonPaginate();
        }, $ttl ?? $this->TTL);
    }

    /**
     * Create a new Project
     */
    public function createProject(array $data): Project
    {
        // TODO:: Remove hardcoded user_id and replace with authenticated user when auth is implemented
        $data['user_id'] = User::first()->id;

        $project = Project::create($data);

        return $project;
    }

    /**
     * Get Project with filtering
     *
     * @param  string  $id  Project ID
     * @param  ProjectFilter  $filters  Filters to apply
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     * @return mixed
     */
    public function getProject(string $id, ProjectFilter $filters, ?int $ttl = null)
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id, $filters);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id, $filters) {
            $p = Project::filter($filters);

            if (\Str::isUlid($id)) {
                return $p->findOrFail($id);
            }

            return $p->where('slug', $id)->firstOrFail();
        }, $ttl ?? $this->TTL);
    }

    /**
     * Get Project by ID without filtering
     *
     * @param  string  $id  Project ID
     * @param  int|null  $ttl  Optional TTL in seconds (null = forever)
     */
    public function getProjectById(string $id, ?int $ttl = null): Project
    {
        $cacheKey = $this->getItemCacheKey($this->TYPE, $id);
        $tags = $this->getCacheTags($this->TYPE, $id);

        return $this->cacheRemember($cacheKey, $tags, function () use ($id) {
            if (\Str::isUlid($id)) {
                return Project::findOrFail($id);
            } else {
                return Project::where('slug', $id)->firstOrFail();
            }
        }, $ttl ?? $this->TTL);
    }

    /**
     * Update Project
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    /**
     * Delete Project
     */
    public function deleteProject(Project $project): void
    {
        $project->delete();
    }
}
