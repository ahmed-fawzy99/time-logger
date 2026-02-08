<?php

namespace App\Http\Filters\Api\V1\Filters;

use App\Http\Filters\Api\V1\QueryFilter;

class ProjectFilter extends QueryFilter
{
    protected array $sortable = [
        'id',
        'userId' => 'user_id',
        'name',
        'slug',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    /*
     * Filter by ID
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function id($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterId($value, 'id');
    }

    /*
     * Filter by user_id
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function userId($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterId($value, 'user_id');
    }

    /*
     * Filter by name
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function name($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterText($value, 'name');
    }

    /*
     * Filter by slug
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function slug($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterEnum($value, 'slug');
    }

    /*
     * Filter by description
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function description($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterText($value, 'description');
    }

    /*
    * Add total time frames count filter
    * @param string $value
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function timeFramesCount(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->builder->withCount('timeFrames');
    }

    /*
    * Add total time entries count filter
    * @param string $value
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function timeEntriesCount(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->builder->withCount('timeEntries');
    }
}
