<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
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
            'type' => 'project',
            'attributes' => [
                'name' => $this->name,
                'slug' => $this->slug,
                'timeFramesCount' => $this->time_frames_count,
                'timeEntriesCount' => $this->time_entries_count,
                'description' => $this->description,
                'additionalProperties' => $this->additional_properties,
            ],
            'links' => [
                'self' => route('projects.show', ['id' => $this->id]),
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user_id,
                    ],
                    'links' => [
                        //  'self' => route('users.show', ['id' => $this->user_id]),
                    ],
                ],
            ],
            'includes' => [
                'timeFrames' => $this->whenLoaded('timeFrames', function () {
                    return TimeFrameResource::collection($this->timeFrames);
                }),
                'timeEntries' => $this->whenLoaded('timeEntries', function () {
                    return TimeEntryResource::collection($this->timeEntries);
                }),
            ],
        ];
    }
}
