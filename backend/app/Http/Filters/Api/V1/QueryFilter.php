<?php

namespace App\Http\Filters\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Base class for filtering queries
 */
abstract class QueryFilter
{
    public Builder $builder;

    public Request $request;

    protected array $sortable;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /*
     * Handles loading of relationships. This function is invoked when the query parameter include=relationship is passed.
     */

    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
    }

    /*
     * Handles loading of relationships' count.
     * This function is invoked when the query parameter includeCount=relationship is passed.
     */

    protected function include($value)
    {
        $relationships = explode(',', $value);
        $relationships = array_map(function ($relationship) {
            return str_replace(';', ',', $relationship);
        }, $relationships);

        return $this->builder->with($relationships);
    }

    /*
     * the function that gets invoked when the query paramater filter[attribute]=value is passed.
     * $params is an array of query parameters. $key is the column name and $value is the value to filter by.
     */

    protected function includeCount($value): Builder
    {
        $relationships = explode(',', $value);

        return $this->builder->withCount($relationships);
    }

    /*
     * the function that gets invoked when you want to add a specific calculated field to the query. add={`fieldName`}
     */

    protected function filter($params)
    {
        foreach ($params as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            } // Handle nested filters like filter[attribute:subattribute]=value
            elseif (method_exists($this, explode(':', $key)[0])) {
                $f = explode(':', $key);
                $method = $f[0];
                $this->$method($f[1], $value);
            }
        }
    }

    /*
     * sorts by the columns passed in the query parameter sort. This function handles mapping of the columns to the database columns as well.
     * Gets invoked when the query parameter sort=column1,-column2 is passed.
    **/

    protected function add($params): void
    {

        $params = explode(',', $params);

        foreach ($params as $param) {
            if (method_exists($this, $param)) {
                $this->$param('1');
            }

        }
    }

    // applies the filters to the query builder

    protected function sort($value)
    {
        $columns = explode(',', $value);
        foreach ($columns as $column) {
            $dir = 'asc';
            // skip columns that start with _ as they custom sorts and are not db columns
            if (str_starts_with($column, '_')) {
                continue;
            }
            if (str_starts_with($column, '-')) {
                $dir = 'desc';
                $column = substr($column, 1);
            }
            $dbColName = $this->sortable[$column] ?? $column;
            $this->builder->orderBy($dbColName, $dir);
        }
    }

    // Helper generic function to filter an id column by a value. utilized by inheriting classes. Supports multiple ids and negation.

    protected function filterId($val, $column = 'id')
    {
        $ids = explode(',', $val);
        if (count($ids) > 1) {
            if (str_starts_with($val, '!')) {
                $ids[0] = substr($ids[0], 1);

                return $this->builder->whereNotIn($column, $ids);
            }

            return $this->builder->whereIn($column, $ids);
        }

        return str_starts_with($val, '!') ?
            $this->builder->whereNot($column, $ids[0]) :
            $this->builder->where($column, $ids[0]);
    }

    // Helper generic function to filter a date column by a value. utilized by inheriting classes
    protected function filterDate($val, $column)
    {
        $dates = explode(',', $val);
        if (count($dates) === 1) {
            $date = str_replace(['>', '<'], '', $dates[0]);
            switch ($val[0]) {
                case '>':
                    return $this->builder->where($column, '>', $date);
                case '<':
                    return $this->builder->where($column, '<', $date);
                default:
                    return $this->builder->whereDate($column, $date);
            }
        }

        return $this->builder->whereBetween($column, $dates);
    }

    // Helper generic function to filter a time column by a value. utilized by inheriting classes
    protected function filterTime($val, $column)
    {
        $times = explode(',', $val);

        if (count($times) === 1) {
            $time = str_replace(['>', '<'], '', $times[0]);

            switch ($val[0]) {
                case '>':
                    return $this->builder->whereTime($column, '>', $time);
                case '<':
                    return $this->builder->whereTime($column, '<', $time);
                default:
                    return $this->builder->whereTime($column, '=', $time);
            }
        }

        return $this->builder->whereBetween($column, $times);
    }

    // Helper generic function to filter a text column by a value. utilized by inheriting classes
    protected function filterText($val, $column)
    {
        $likeStr = str_replace('*', '%', $val);

        return str_starts_with($val, '!') ?
            $this->builder->whereNot($column, 'ILIKE', substr($likeStr, 1)) :
            $this->builder->where($column, 'ILIKE', $likeStr);
    }

    // Helper generic function to filter an enum column by a value. utilized by inheriting classes
    protected function filterEnum($val, $column)
    {
        return str_starts_with($val, '!') ?
            $this->builder->whereNot($column, substr($val, 1)) :
            $this->builder->where($column, $val);
    }

    // Helper generic function to filter a number column by a value. utilized by inheriting classes
    protected function filterNumber($val, $column)
    {
        $vals = explode(',', $val);
        if (count($vals) === 1) {
            $num = str_replace(['>', '<'], '', $vals[0]);
            switch ($val[0]) {
                case '>':
                    return $this->builder->where($column, '>', $num);
                case '<':
                    return $this->builder->where($column, '<', $num);
                default:
                    return $this->builder->where($column, $vals[0]);
            }

        }

        return $this->builder->whereBetween($column, $vals);
    }

    // filter booleans
    protected function filterBoolean($val, $column)
    {
        $bool = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($bool === null) {
            return $this->builder;
        }

        return str_starts_with($val, '!') ?
            $this->builder->whereNot($column, $bool) :
            $this->builder->where($column, $bool);
    }
}
