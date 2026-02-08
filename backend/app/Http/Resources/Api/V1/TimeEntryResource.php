<?php

namespace App\Http\Resources\Api\V1;

use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TimeEntry
 */
class TimeEntryResource extends JsonResource
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
            'type' => 'timeEntry',
            'attributes' => [
                'workDay' => $this->work_day,
                'startTime' => $this->start_time,
                'endTime' => $this->end_time,
                'description' => $this->description,
                'billable' => $this->billable,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                'self' => route('time-entries.show', ['id' => $this->id]),
            ],
            'relationships' => [
                'timeFrame' => [
                    'data' => [
                        'type' => 'timeFrame',
                        'id' => $this->time_frame_id,
                    ],
                    'links' => [
                        'self' => route('time-frames.show', ['id' => $this->time_frame_id]),
                    ],
                ],
            ],
            'includes' => [
                'timeFrame' => $this->whenLoaded('timeFrame', function () {
                    return new TimeFrameResource($this->timeFrame);
                }),
            ],
        ];
    }
}
