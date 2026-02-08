<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Preference
 */
class PreferenceResource extends JsonResource
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
            'type' => 'preference',
            'attributes' => [
                'hourlyRate' => $this->hourly_rate,
                'currency' => $this->currency,
                'weekStart' => $this->week_start,
                ...$this->additional_properties,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                // TODO:: Replace with show when authentication is implemented
                'self' => route('preferences.index'),
            ],
            'relationships' => [],
            'includes' => [],
        ];
    }
}
