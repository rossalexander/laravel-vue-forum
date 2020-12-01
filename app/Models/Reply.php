<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;
    use Favorable, RecordsActivity;

    protected $guarded = [];

    protected $appends = ['favorites_count', 'is_favorited']; // https://laravel.com/docs/8.x/eloquent-serialization#appending-values-to-json

    protected $with = ['owner', 'favorites']; // eager load for every single query on the Reply model

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

}
