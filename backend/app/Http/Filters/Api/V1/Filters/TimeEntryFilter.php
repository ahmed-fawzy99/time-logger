<?php

namespace App\Http\Filters\Api\V1\Filters;

use App\Http\Filters\Api\V1\QueryFilter;

class TimeEntryFilter extends QueryFilter
{
    protected array $sortable = [
        'id',
        'timeFrameId' => 'time_frame_id',
        'workDay' => 'work_day',
        'startTime' => 'start_time',
        'endTime' => 'end_time',
        'description',
        'billable',
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
     * Filter by timeframe_id
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function timeFrameId($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterId($value, 'time_frame_id');
    }

    /*
     * Filter by work_day
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function workDay($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterDate($value, 'work_day');
    }

    /*
     * Filter by start_time
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function startTime($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterTime($value, 'start_time');
    }

    /*
     * Filter by end_time
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function endTime($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterTime($value, 'end_time');
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
     * Filter by billable
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function billable($value): \Illuminate\Database\Eloquent\Builder
    {
        return $this->filterBoolean($value, 'billable');
    }
}
