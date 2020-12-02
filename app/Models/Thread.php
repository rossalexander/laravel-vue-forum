<?php

namespace App\Models;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];
    protected $with = ['owner', 'channel'];
    protected $appends = ['isSubscribed']; // append isSubscribed property to array output whenever this model is called

    protected static function boot()
    {
        parent::boot();

        // For all queries for Thread, apply this scope (include reply count)
        static::addGlobalScope('replyCount', function ($builder) {
            $builder->withCount('replies');
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete(); // pseudo property "each" todo: research
        });
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Add a reply to the thread
     *
     * @param $reply
     * @return Model
     */
    public function addReply($reply)
    {
        // Save the reply.
        $reply = $this->replies()->create($reply);

        $this->subscriptions
            ->filter(function ($sub) use ($reply) {
                return $sub->user_id != $reply->user_id;
            })->each->notify($reply);
//            ->each(function ($sub) use ($reply) {
//                $sub->user->notify(new ThreadWasUpdated($this, $reply));
//            });

//        foreach ($this->subscriptions as $subscription) {
//            if ($subscription->user_id != $reply->user_id) {
//                $subscription->user->notify(new ThreadWasUpdated($this, $reply));
//            }
//        }

        return $reply;
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * Subscribe a user to the current thread.
     *
     * @param int|null $user_id
     * @return $this
     */
    public function subscribe($user_id = null)
    {
        $this->subscriptions()->create([
            'user_id' => $user_id ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($user_id = null)
    {
        $this->subscriptions()
            ->where('user_id', $user_id ?: auth()->id())
            ->delete();
    }

    public function getIsSubscribedAttribute() // custom Eloquent accessor
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists(); // We don't need to fetch any data. We just need to know if there is a matching record.
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

}
