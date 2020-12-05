<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Thread
 * @package App\Models
 * @property $channel
 * @property $id
 * @property $subscriptions
 * @property $user_id
 */
class Thread extends Model
{
    use HasFactory, RecordsActivity;

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relationships to always eager load.
     *
     * @var string[]
     */
    protected $with = ['owner', 'channel'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var string[]
     */
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

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function path(): string
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Add a reply to the thread
     *
     * @param $reply
     * @return Model
     */
    public function addReply($reply): Model
    {
        $reply = $this->replies()->create($reply);
        $this->touch(); // todo: Find out why $this isn't updating without touching it.

        $this->notifySubscribers($reply);

        return $reply;
    }

    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each
            ->notify($reply);
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
    public function subscribe($user_id = null): Thread
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

    public function getIsSubscribedAttribute(): bool
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists(); // We don't need to fetch any data. We just need to know if there is a matching record.
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function hasUpdatesFor(User $user): bool
    {
        $key = $user->visitedThreadCacheKey($this);
        return $this->updated_at > cache($key);
    }


}
