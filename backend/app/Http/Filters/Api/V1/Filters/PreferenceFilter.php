<?php

namespace App\Http\Filters\Api\V1\Filters;

use App\Http\Filters\Api\V1\QueryFilter;

class PreferenceFilter extends QueryFilter
{
    protected array $sortable = [
        'id',
        'properties',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    /*
     * Filter by ID
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function id($value)
    {
        return $this->filterId($value, 'id');
    }
}
