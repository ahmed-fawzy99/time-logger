<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Filters\Api\V1\Filters\TimeFrameFilter;
use App\Http\Requests\TimeFrameRequest;
use App\Http\Resources\Api\V1\TimeFrameResource;
use App\Services\V1\TimeFrameServices;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class TimeFrameController extends ApiController
{
    public function __construct(protected TimeFrameServices $timeFrameServices) {}

    /**
     * Get All Timeframes (Paginated).
     *
     *  Defaults to 25 items per page.
     *
     *  To get specific page or size, use the following query parameters:
     *
     *  `?page[number]=x&page[size]=y`
     */
    public function index(TimeFrameFilter $filters)
    {
        try {
            $timeFrames = $this->timeFrameServices->getTimeframes($filters);

            return TimeFrameResource::collection($timeFrames);
        } catch (\Exception $e) {
            return $this->error('Timeframes Retrieval Error', $e->getMessage(), 500);
        }
    }

    /**
     * Store a New Affiliate.
     */
    public function store(TimeFrameRequest $request)
    {
        try {

            $timeFrame = $this->timeFrameServices->createTimeframe($request->validated());

            return $this->ok('TimeFrame Created', new TimeFrameResource($timeFrame), 201);
        } catch (\Exception $e) {

            return $this->error('TimeFrame Creation Error', $e->getMessage(), 500, $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, TimeFrameFilter $filters)
    {
        try {
            $timeFrame = $this->timeFrameServices->getTimeframe($id, $filters);

            return $this->ok('TimeFrame Retrieved Successfully', new TimeFrameResource($timeFrame));
        } catch (ModelNotFoundException $e) {
            return $this->error('TimeFrame not found', null, 404);
        } catch (\Exception $e) {
            return $this->error('TimeFrame Error', null, 500, $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getInvoice(string $id)
    {
        try {
            $mediaUrl = $this->timeFrameServices->getInvoice($id);

            return $this->ok('TimeFrame Invoice Retrieved Successfully', [
                'invoiceUrl' => $mediaUrl,
            ]);
        } catch (BadRequestException $e) {
            return $this->error('Time Frame Invoice Error', $e->getMessage(), 400);
        } catch (ModelNotFoundException $e) {
            return $this->error('Time Frame not found', null, 404);
        } catch (\Exception $e) {
            return $this->error('Time Frame Invoice Error', null, 500, $e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimeFrameRequest $request, string $id)
    {
        try {
            // We're inserting the filter here to load the related relations for the returned TimeFrameResource().
            // This is necessary to ensure that the totalBillableSeconds attribute is included in the response after the update.
            $filters = new TimeFrameFilter(new \Illuminate\Http\Request([
                'include' => 'timeEntries,project:id;name;slug,media',
                'add' => 'totalBillableSeconds',
            ]));
            $timeFrame = $this->timeFrameServices->getTimeframe($id, $filters);
            $timeFrame = $this->timeFrameServices->updateTimeframe($timeFrame, $request->validated());

            return $this->ok('TimeFrame Updated', new TimeFrameResource($timeFrame));
        } catch (ModelNotFoundException $e) {
            return $this->error('TimeFrame not found', null, 404);
        } catch (\Exception $e) {

            return $this->error('TimeFrame Update Error', $e->getMessage(), 500, $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $timeFrame = $this->timeFrameServices->getTimeframeById($id);
            $this->timeFrameServices->deleteTimeframe($timeFrame);

            return $this->ok('TimeFrame deleted successfully', null);
        } catch (ModelNotFoundException $e) {
            return $this->error('TimeFrame not found', null, 404);
        } catch (\Exception $e) {
            return $this->error('TimeFrame deletion failed', null, 500, $e);
        }
    }
}
