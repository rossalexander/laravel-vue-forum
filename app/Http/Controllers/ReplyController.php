<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Spam;
use App\Models\Thread;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * Class ReplyController
 * @package App\Http\Controllers
 */
class ReplyController extends Controller
{
    /**
     * ReplyController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * @param $channelID
     * @param Thread $thread
     * @return LengthAwarePaginator
     */
    public function index($channelID, Thread $thread): LengthAwarePaginator
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $channelID
     * @param Thread $thread
     * @param Spam $spam
     * @return Model|RedirectResponse
     * @throws ValidationException
     */
    public function store($channelID, Thread $thread, Spam $spam): Model|RedirectResponse
    {
        $this->validate(request(), ['body' => 'required']);
        $spam->detect(request('body'));

        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);

        if (request()->expectsJson()) {
            return $reply->load('owner'); // eager load owner for axios reply post
        }

        return back()->with('flash', 'Your reply has been left.');
    }

    /**
     * @param Reply $reply
     * @throws AuthorizationException
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->update(['body' => request('body')]);
    }

    /**
     * @param Reply $reply
     * @return Response|RedirectResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy(Reply $reply): Response|RedirectResponse
    {
        $this->authorize('update', $reply);
        $reply->delete();
        // If this request is Ajax
        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

}
