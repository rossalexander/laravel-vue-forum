<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ReplyController extends Controller
{
    /**
     * ReplyController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelID, Thread $thread)
    {
        return $thread->replies()->paginate(1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $channelID
     * @param Thread $thread
     * @return RedirectResponse|void
     * @throws ValidationException
     */
    public function store($channelID, Thread $thread)
    {
        $this->validate(request(), [
            'body' => 'required',
        ]);

        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);

        if (request()->expectsJson()) {
            return $reply->load('owner'); // eager load owner for axios reply post
        }

        return back()->with('flash', 'Your reply has been left.');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->update(['body' => request('body')]);
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->delete();
        // If this request is Ajax
        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        // Else
        return back();
    }

}
