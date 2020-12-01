<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    public function test_guests_cannot_favorite_anything()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->withoutExceptionHandling();
        $this->post('/replies/1/favorites');
        //    ->assertRedirect('/login');
    }

    public function test_an_auth_user_can_favorite_any_reply()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        $this->post('replies/' . $reply->id . '/favorites');

        // expect to see at least one item in this favorites relationship
        $this->assertCount(1, $reply->favorites);
    }

    public function test_an_auth_user_can_unfavorite_a_reply()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        // we could use $reply->favorite() from our own API and then we would not need fresh(), below
        $this->post('replies/' . $reply->id . '/favorites');
        $this->assertCount(1, $reply->favorites);

        $this->delete('replies/' . $reply->id . '/favorites');
        $this->assertCount(0, $reply->fresh()->favorites); // eager loaded above, so we have to get fresh()
    }

    public function test_an_auth_user_can_only_favorite_a_reply_once()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(User::factory()->create());

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $e) {
            $this->fail('Did not expect to insert the same record set twice.');
        }
//        dd(Favorite::all()->toArray());

        // expect to see ONLY one item in this favorites relationship
        $this->assertCount(1, $reply->favorites);
    }
}
