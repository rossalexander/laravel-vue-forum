<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    // For every test, migrate if needed. Once test has completed, roll back the migration.
    use DatabaseMigrations;


    protected $thread;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = Thread::factory()->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_a_user_can_view_all_threads()
    {

        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    public function test_a_user_can_view_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    public function test_user_can_filter_threads_by_channel()
    {
        //$this->actingAs(User::factory()->create());
        $channel = Channel::factory()->create();

        $threadInChannel = create(Thread::class, ['channel_id' => $channel->id]);

        $threadNotInChannel = create(Thread::class);


        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    function test_a_user_can_filter_threads_by_any_username()
    {
        $this->be(User::factory()->create(['name' => 'JohnDoe']));

        $threadByJohn = create(Thread::class, ['user_id' => auth()->id()]);
        $threadNotByJohn = create(Thread::class);

        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    function test_a_user_can_filter_threads_by_popularity()
    {
        // Given we have three threads with 2 replies, 3 replies, and 0 replies.
        $threadWithTwoReplies = Thread::factory()->create();
        Reply::factory()->times(2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $threadWithThreeReplies = Thread::factory()->create();
        Reply::factory()->times(3)->create(['thread_id' => $threadWithThreeReplies->id]);

        //dd($threadWithTwoReplies->replies);

        $threadWithNoReplies = $this->thread;

        // When we filter all threads by popularity
        $response = $this->getJson('threads?popular=1')->json();

        //dd($response);
        // Then they should be returned from most replies to least
        $this->assertEquals([3, 2, 0], array_column($response, 'replies_count'));
    }

    function test_a_user_can_filter_threads_by_unanswered()
    {
        $answered_thread = Thread::factory()->create();
        Reply::factory()->create(['thread_id' => $answered_thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();
        $this->assertCount(1, $response);
    }

    function test_a_user_can_request_all_replies_for_a_thread()
    {
        $this->withoutExceptionHandling();
        $thread = Thread::factory()->create();
        $reply = Reply::factory()->times(2)->create(['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path() . '/replies')->json();
        //dd($response);

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }
}
