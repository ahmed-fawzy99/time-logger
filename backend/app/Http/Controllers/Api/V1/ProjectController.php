<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Filters\Api\V1\Filters\ProjectFilter;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Services\V1\ProjectServices;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends ApiController
{
    public function __construct(protected ProjectServices $projectServices) {}

    /**
     * Get All Projects (Paginated).
     *
     *  Defaults to 25 items per page.
     *
     *  To get specific page or size, use the following query parameters:
     *
     *  `?page[number]=x&page[size]=y`
     */
    public function index(ProjectFilter $filters)
    {
        try {
            $projects = $this->projectServices->getProjects($filters);

            return ProjectResource::collection($projects);
        } catch (\Exception $e) {
            return $this->error('Projects Retrieval Error', null, 500);
        }
    }

    /**
     * Store a New Project.
     */
    public function store(ProjectRequest $request)
    {
        try {

            $project = $this->projectServices->createProject($request->validated());

            return $this->ok('Project Created', new ProjectResource($project), 201);
        } catch (\Exception $e) {

            return $this->error('Project Creation Error', $e->getMessage(), 500, $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, ProjectFilter $filters)
    {
        try {
            $project = $this->projectServices->getProject($id, $filters);

            return $this->ok('Project Retrieved Successfully', new ProjectResource($project));
        } catch (ModelNotFoundException $e) {
            return $this->error('Project not found', null, 404);
        } catch (\Exception $e) {
            return $this->error('Project Error', $e->getMessage(), 500, $e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        try {
            $project = $this->projectServices->getProjectById($id);
            $project = $this->projectServices->updateProject($project, $request->validated());

            return $this->ok('Project Updated', new ProjectResource($project));
        } catch (ModelNotFoundException $e) {
            return $this->error('Project not found', null, 404);
        } catch (\Exception $e) {

            return $this->error('Project Update Error', null, 500, $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $project = $this->projectServices->getProjectById($id);
            $this->projectServices->deleteProject($project);

            return $this->ok('Project deleted successfully', null);
        } catch (ModelNotFoundException $e) {
            return $this->error('Project not found', null, 404);
        } catch (\Exception $e) {
            return $this->error('Project deletion failed', null, 500, $e);
        }
    }
}
