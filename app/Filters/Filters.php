<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{

    protected $request, $builder;
    protected $filters = [];

    /**
     * ThreadFilters constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) // 1. Accept request
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        // Get the filters (key/value pairs)
        foreach ($this->getFilters() as $filter => $value) {
            // If the filter exists
            if (method_exists($this, $filter)) {
                // Trigger the filter and pass through the value
                $this->$filter($this->request->$filter);
            }
        }

        return $this->builder;
    }

    public function getFilters()
    {
        return $this->request->only($this->filters);
    }

}
