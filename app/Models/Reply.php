<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Reply
 * @package App\Models
 * @property $id
 * @property $user_id
 * @property $thread
 */
class Reply extends Model
{
    use HasFactory;
    use Favorable, RecordsActivity;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $appends = ['favorites_count', 'is_favorited'];

    /**
     * @var string[]
     */
    protected $with = ['owner', 'favorites']; // eager load for every single query on the Reply model

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

}
