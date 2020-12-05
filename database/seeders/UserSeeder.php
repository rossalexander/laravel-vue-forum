<?php

namespace Database\Seeders;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'John',
            'email' => 'john@email.com',
            'password' => Hash::make('password'),
        ]);

        // todo: sign in Jane

        User::factory()
            ->times(10)
            ->create()
            ->each(function ($user) {
                $threads = Thread::factory()
                    ->times(rand(1, 3))
                    ->create(['user_id' => $user->id])
                    ->each(function ($thread) {
                        Reply::factory()->times(rand(0,4))->create(['thread_id' => $thread->id]);
                    });
                $user->threads()->saveMany($threads);
            });
    }
}
