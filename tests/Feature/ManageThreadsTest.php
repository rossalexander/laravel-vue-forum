<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ManageThreadsTest extends TestCase
{
    use DatabaseMigrations;

    function test_guest_can_not_create_threads()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->withoutExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    function test_auth_user_can_create_threads()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->make();

        $response = $this->post('/threads', $thread->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    function test_a_thread_requires_a_title()
    {
        $this->expectException(ValidationException::class);
        $this->publishThread(['title' => null]);
    }

    function test_a_thread_requires_a_body()
    {
        $this->expectException(ValidationException::class);
        $this->publishThread(['body' => null]);
    }

    function test_a_thread_requires_a_valid_channel()
    {
        Channel::factory()->times(2)->create();

        $this->expectException(ValidationException::class);
        $this->publishThread(['channel_id' => null]);

        $this->expectException(ValidationException::class);
        $this->publishThread(['channel_id' => 999]);
    }

    public function publishThread($overrides = [])
    {
        $this->withoutExceptionHandling();
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->make($overrides);

        return $this->post('/threads', $thread->toArray());
    }

    public function test_unauthorized_users_may_not_delete_threads()
    {
//        $this->expectException('Illuminate\Auth\AuthenticationException');
//        $this->withoutExceptionHandling();

        $thread = Thread::factory()->create();

        $this->delete($thread->path())->assertRedirect('/login');

        $this->actingAs(User::factory()->create());
        $this->delete($thread->path())->assertStatus(403);

    }

    public function test_authorized_users_may_delete_threads()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $thread = Thread::factory()->create(['user_id' => $user->id]);
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        /*$this->assertDatabaseMissing('activities', [
            'subject_id' => $thread->id,
            'subject_type' => get_class($thread),
        ]);

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $reply->id,
            'subject_type' => get_class($reply),
        ]);*/

        $this->assertEquals(0, Activity::count());
    }
}
