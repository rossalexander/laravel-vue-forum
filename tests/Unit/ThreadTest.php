<?php

namespace Tests\Unit;


use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = Thread::factory()->create();
    }

    function test_a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    function test_a_thread_has_a_string_path()
    {
        $this->assertEquals("/threads/{$this->thread->channel->slug}/{$this->thread->id}", $this->thread->path());
    }

    function test_a_thread_has_an_owner()
    {
        $this->assertInstanceOf(User::class, $this->thread->owner);
    }

    function test_a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    function test_a_thread_notifies_all_subscribers_when_a_reply_is_added()
    {
        // Fake the notification
        Notification::fake();

        $this->actingAs(User::factory()->create());

        $this->thread
            ->subscribe()
            ->addReply([
                'body' => 'foobar',
                'user_id' => 999
            ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);

    }

    function test_a_thread_belongs_to_a_channel()
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    function test_a_thread_can_be_subscribed_to()
    {
        $this->withoutExceptionHandling();

        // Given we have a thread
        $thread = $this->thread;

        // And an auth user
        $user = User::factory()->create();
        $this->actingAs($user);

        // When the user subscribes to the thread
        $thread->subscribe();

        // Then we should be able to fetch all threads that the user has subscribed to.
        $this->assertEquals(1, $thread->subscriptions()->where('user_id', $user->id)->count());
    }

    function test_a_thread_can_be_unsubscribed_from()
    {
        // Given we have a thread
        $thread = $this->thread;

        // And an auth user who is subscribed to the thread
        $user = User::factory()->create();
        $this->actingAs($user);
        $thread->subscribe($user->id);

        // When the thread is unsubscribed from
        $thread->unsubscribe($user->id);

        // The thread should have no subscriptions
        $this->assertCount(0, $thread->subscriptions);
    }

    function test_a_thread_knows_if_auth_user_is_subscribed()
    {
        // Given that we have a thread
        $thread = $this->thread;

        // And an authenticated user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Before the user subscribes, the user should not be subscribed
        $this->assertFalse($thread->isSubscribed);

        // But after the user subscribes, the user should be subscribed
        $thread->subscribe();
        $this->assertTrue($thread->isSubscribed);
    }


}
