<?php


namespace App\Models;


use ReflectionException;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) {
            //todo: learn how to try catch
//            throw new \Exception('User not signed in. (RecordsActivity trait).');
            return;
        }

        // Whenever a thread is created in the database todo: video 25
        foreach (static::getActivitiesToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        // Important: this fires on the model (e.g., $favorite->delete() works (instance).
        // Won't work on a custom SQL query (e.g., our unfavorite method on our Favorable trait), because at no point
        // did we build up an instance of Favorite. We just go straight to the DB in that case, which bypasses this.
        static::deleting(function ($model) {
            $model->activity()->delete();
        });

    }

    public static function getActivitiesToRecord()
    {
        return ['created']; // names match the model events that Laravel provides
    }


    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }


    /**
     * @return mixed
     */
    public function activity()
    {
        return $this->morphMany('App\Models\Activity', 'subject');
    }

    /**
     * @param $event
     * @return string
     * @throws ReflectionException
     */
    protected function getActivityType($event): string
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());
        return "{$event}_{$type}";
    }
}
