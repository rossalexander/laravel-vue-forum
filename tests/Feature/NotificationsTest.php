<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Database\Factories\DatabaseNotificationFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    function test_a_notifications_is_prepared_when_a_subscribed_thread_receives_a_new_reply_not_by_current_user()
    {
        $user = auth()->user();
        $thread = Thread::factory()->create()->subscribe();
        $this->assertCount(0, $user->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply here'
        ]);

        $this->assertCount(0, $user->fresh()->notifications);

        $another_reply = $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'Some other user left this reply.'
        ]);

        $this->assertCount(1, $user->fresh()->notifications);
    }

    function test_a_user_can_fetch_their_unread_notifications()
    {
        DatabaseNotificationFactory::new()->create();

        $this->assertCount(
            1,
            $this->getJson("/profiles/" . auth()->user()->name . "/notifications/")->json());
    }

    function test_a_user_can_mark_a_notification_as_read()
    {
        DatabaseNotificationFactory::new()->create();

        $user = auth()->user();

        tap(auth()->user(), function ($user) {
            $this->assertCount(1, $user->unreadNotifications);

            $this->delete("/profiles/{$user->name}/notifications/" . $user->unreadNotifications->first()->id);

            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });
    }

}
