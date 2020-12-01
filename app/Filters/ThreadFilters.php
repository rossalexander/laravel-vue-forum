<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Http\Request;

class ThreadFilters extends Filters
{

    // Whenever we need to add a filter, we can just update this array.
    protected $filters = [
        'by',
        'popular'
    ];

    /**
     * Filter the query by a given username
     * @param string $username
     * @return mixed
     */
    protected function by($username)
    {
        // 2. If the request has a username, find that user (or fail)
        $user = User::where('name', $username)->firstOrFail();

        // 3. Apply that user id to the query (all threads where the user id is equal to this one)
        return $this->builder->where('user_id', $user->id);
    }


    /**
     * Filter the query according to most popular threads.
     *
     */
    protected function popular()
    {
        $this->builder->getQuery()->orders = []; // clear the default orderBy (created_at)

        $this->builder->orderBy('replies_count', 'desc');
    }
}

