<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    private $str;

    public function test_unauth_user_can_not_participate_in_forum_threads()
    {
        $this->expectException(AuthenticationException::class);
        $this->withoutExceptionHandling();
        $this->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    public function test_auth_user_can_participate_in_forum_threads()
    {
        $this->actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make();

        //dd($thread->path() .'/replies');

        $this->post($thread->path() . '/replies', $reply->toArray());

//        The replies are now being loaded with JS, so we can't use PHPUnit to test this (with this setup).
//        $this->get($thread->path())
//            ->assertSee($reply->body);

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
    }

    function test_a_reply_requires_a_body()
    {
        $this->actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make(['body' => null]);

        //$this->expectException(ValidationException::class);
        $this->post($thread->path() . '/replies', $reply->toArray())->assertSessionHasErrors('body');
    }

    function test_unauthorized_users_cannot_delete_replies()
    {
        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $user = User::factory()->create();
        $this->actingAs($user);
        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        $this->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    public function test_auth_users_can_delete_replies()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $thread = Thread::factory()->create(['user_id' => $user->id]);
        $reply = Reply::factory()->create(['thread_id' => $thread->id, 'user_id' => $user->id]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);
        $this->assertDeleted('replies', ['id' => $reply->id]);
    }

    function test_unauthorized_users_cannot_update_replies()
    {
        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);
        $updatedReply = 'You been changed, fool.';
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply])
            ->assertRedirect('login');

        $user = User::factory()->create();
        $this->actingAs($user);
        $this->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    function test_auth_users_can_update_replies()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id, 'user_id' => $user->id]);

        $updatedReply = 'You been changed, fool.';
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply]);
        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updatedReply]);
    }

}
