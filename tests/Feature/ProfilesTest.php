<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_user_has_a_profile()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
    }

    public function test_display_all_threads_owned_by_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $this->get("/profiles/{$user->name}")
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
