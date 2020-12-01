<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Reply;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * FavoriteController constructor.
     */
    public function __construct()
    {
        // Must be signed in to favorite anything.
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Reply $reply
     * @return Model|null
     */
    public function store(Reply $reply)
    {
        $reply->favorite();

        return back();

//        Favorite::create([
//            'user_id' => auth()->id(), // we just need to set the user_id
//            'favorited_id' => $reply->id, // because we're using a polymorphic relationship, Eloquent will automatically
//            'favorited_type' => get_class($reply) // set the ID and the class for the reply
//        ]);
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}
