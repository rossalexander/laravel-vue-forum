<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    function test_a_notifications_is_prepared_when_a_subscribed_thread_receives_a_new_reply_not_by_current_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

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
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $thread = Thread::factory()->create()->subscribe();

        $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'Some other user left this reply.'
        ]);

        $response = $this->getJson("/profiles/{$user->name}/notifications/")->json();

        $this->assertCount(1, $response);
    }

    function test_a_user_can_mark_a_notification_as_read()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $thread = Thread::factory()->create()->subscribe();

        $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'Some other user left this reply.'
        ]);

        $this->assertCount(1, $user->unreadNotifications);

        $notificationId = $user->unreadNotifications->first()->id;

        $this->delete("/profiles/{$user->name}/notifications/{$notificationId}");

        $this->assertCount(0, $user->fresh()->unreadNotifications);

    }
}
