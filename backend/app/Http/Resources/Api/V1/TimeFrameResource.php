<?php

namespace App\Http\Resources\Api\V1;

use App\Models\TimeFrame;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TimeFrame
 */
class TimeFrameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'timeFrame',
            'attributes' => [
                'startDate' => $this->start_date,
                'endDate' => $this->end_date,
                'name' => $this->name,
                'status' => $this->status,
                'notes' => $this->notes,
                'invoiceUrl' => $this->invoice_url,
                'hourlyRate' => $this->hourly_rate,
                'currency' => $this->currency,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,

                // flatten getMutatorsForApi
                ...$this->mutators,
            ],
            'links' => [
                'self' => route('time-frames.show', ['id' => $this->id]),
            ],
            'relationships' => [
                'project' => [
                    'data' => [
                        'type' => 'project',
                        'id' => $this->project_id,
                    ],
                    'links' => [
                        'self' => route('projects.show', ['id' => $this->project_id]),
                    ],
                ],
            ],
            'includes' => [
                'timeEntries' => $this->whenLoaded('timeEntries', function () {
                    return TimeEntryResource::collection($this->timeEntries);
                }),
                'project' => $this->whenLoaded('project', function () {
                    return new ProjectResource($this->project);
                }),
            ],
        ];
    }
}
