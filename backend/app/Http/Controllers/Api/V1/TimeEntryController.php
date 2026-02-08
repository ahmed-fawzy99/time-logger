<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Filters\Api\V1\Filters\TimeEntryFilter;
use App\Http\Requests\TimeEntryRequest;
use App\Http\Resources\Api\V1\TimeEntryResource;
use App\Services\V1\TimeEntryServices;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TimeEntryController extends ApiController
{
    public function __construct(protected TimeEntryServices $timeEntryServices) {}

    /**
     * Get All Time Entries (Paginated).
     *
     *  Defaults to 25 items per page.
     *
     *  To get specific page or size, use the following query parameters:
     *
     *  `?page[number]=x&page[size]=y`
     */
    public function index(TimeEntryFilter $filters)
    {
        try {
            $timeEntries = $this->timeEntryServices->getTimeEntries($filters);

            return TimeEntryResource::collection($timeEntries);
        } catch (\Exception $e) {
            return $this->error('Time Entries Retrieval Error', null, 500);
        }
    }

    /**
     * Store a New Time Entry.
     */
    public function store(TimeEntryRequest $request)
    {
        try {

            $timeEntry = $this->timeEntryServices->createTimeEntry($request->validated());

            return $this->ok('Time Entry Created', new TimeEntryResource($timeEntry), 201);
        } catch (\Exception $e) {

            return $this->error('Time Entry Creation Error', $e->getMessage(), 500, $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, TimeEntryFilter $filters)
    {
        try {
            $timeEntry = $this->timeEntryServices->getTimeEntry($id, $filters);

            return $this->ok('Time Entry Retrieved Successfully', new TimeEntryResource($timeEntry));
        } catch (ModelNotFoundException $e) {
            return $this->error('Time Entry not found', null, 404);
        } catch (\Exception $e) {
            return $this->error('Time Entry Error', null, 500, $e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimeEntryRequest $request, string $id)
    {
        try {
            $timeEntry = $this->timeEntryServices->getTimeEntryById($id);
            $timeEntry = $this->timeEntryServices->updateTimeEntry($timeEntry, $request->validated());

            return $this->ok('Time Entry Updated', new TimeEntryResource($timeEntry));
        } catch (ModelNotFoundException $e) {
            return $this->error('Time Entry not found', null, 404);
        } catch (\Exception $e) {

            return $this->error('Time Entry Update Error', null, 500, $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $timeEntry = $this->timeEntryServices->getTimeEntryById($id);
            $this->timeEntryServices->deleteTimeEntry($timeEntry);

            return $this->ok('Time Entry deleted successfully', null);
        } catch (ModelNotFoundException $e) {
            return $this->error('Time Entry not found', null, 404);
        } catch (\Exception $e) {
            return $this->error('Time Entry deletion failed', null, 500, $e);
        }
    }
}
