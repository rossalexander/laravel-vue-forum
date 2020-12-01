<?php


namespace App\Models;


trait Favorable
{

    protected static function bootFavorable()
    {
        // When deleting the associated model (could be a reply or something else)
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    public function isFavorited()
    {
        return !!$this->favorites->where('user_id', auth()->id())->count();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if (!$this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);
        }
        return null;
    }

    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        // We're on the reply instance
        // So get the reply's favorites.
        // But only the one where the user_id is the current user's.
        // And delete it.
        // $this->favorites()->where($attributes)->delete();
        // Update: We change the unfavorite method to first get a collection and operate on that so that our model
        // event works (bootRecordsActivity) (rather than doing custom SQL

        // https://laravel.com/docs/8.x/collections#higher-order-messages
        $this->favorites()->where($attributes)->get()->each->delete();
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }
}
