<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    public function test_activity_recorded_when_thread_is_created()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $thread = Thread::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Models\Thread'
        ]);

        $activity = Activity::first();
        $this->assertEquals($activity->subject->id, $thread->id);
    }

    public function test_activity_recorded_when_reply_is_created()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $thread = Thread::factory()->create(['user_id' => $user->id]);
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);
        $this->assertEquals(2, Activity::count());
    }

    public function test_fetch_activity_feed_for_any_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Thread::factory()->create([
            'user_id' => $user->id
        ]);

        Thread::factory()->create([
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subWeek()
        ]);


        $user->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feed = Activity::feed($user, 50);

        // dd($feed->keys());

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
}
