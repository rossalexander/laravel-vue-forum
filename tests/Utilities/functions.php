<?php

use App\Models\User;

function create($class, $attributes = [], $times = null)
{
    return $class::factory()->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return $class::factory()->make($attributes);
}
