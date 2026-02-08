<?php

namespace App\Http\Filters\Api\V1\Filters;

use App\Http\Filters\Api\V1\QueryFilter;

class TimeFrameFilter extends QueryFilter
{
    protected array $sortable = [
        'id',
        'projectId' => 'project_id',
        'startDate' => 'start_date',
        'endDate' => 'end_date',
        'name',
        'status',
        'notes',
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

    /*
     * Filter by project_id
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function projectId($value)
    {
        return $this->filterId($value, 'project_id');
    }

    /*
     * Filter by start_date
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function startDate($value)
    {
        return $this->filterDate($value, 'start_date');
    }

    /*
     * Filter by end_date
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function endDate($value)
    {
        return $this->filterDate($value, 'end_date');
    }

    /*
     * Filter by name
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function name($value)
    {
        return $this->filterText($value, 'name');
    }

    /*
     * Filter by status
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function status($value)
    {
        return $this->filterEnum($value, 'status');
    }

    /**
     * Add Billable Seconds to the query
     */
    public function totalBillableSeconds(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->builder->withTotalBillableSeconds();
    }
}
