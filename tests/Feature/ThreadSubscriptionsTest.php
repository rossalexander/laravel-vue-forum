<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ThreadSubscriptionsTest extends TestCase
{
    use DatabaseMigrations;

    function test_a_user_can_subscribe_to_a_thread()
    {
        // $this->withoutExceptionHandling();

        // Give that we have a thread
        $thread = Thread::factory()->create();

        // and a user subscribes to that thread
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1, $thread->fresh()->subscriptions);
    }

    function test_a_user_can_unsubscribe_from_a_thread()
    {
        $this->withoutExceptionHandling();
        // Given that we have a thread
        $thread = Thread::factory()->create();

        // And an auth user who is subscribed to that thread
        $user = User::factory()->create();
        $this->actingAs($user);
        $thread->subscribe();

        // When the user unsubscribes
        $this->delete($thread->path() . '/subscriptions');
        $this->assertCount(0, $thread->subscriptions);
    }
}
