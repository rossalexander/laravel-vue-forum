<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Inspections\Spam;
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
     * @return Model|RedirectResponse
     * @throws ValidationException
     */
    public function store(string $channelID, Thread $thread)
    {
        $this->validateReply();

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
     * @throws AuthorizationException|ValidationException
     * @throws Exception
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $this->validateReply();

        $reply->update(['body' => request('body')]);
    }

    /**
     * @param Reply $reply
     * @return Application|ResponseFactory|RedirectResponse|Response
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->delete();
        // If this request is Ajax
        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }


    /**
     * Validate reply body and check for spam
     *
     * @throws ValidationException
     * @throws Exception
     */
    protected function validateReply(): void
    {
        $this->validate(request(), ['body' => 'required']);

        resolve(Spam::class)->detect(request('body'));
    }

}
