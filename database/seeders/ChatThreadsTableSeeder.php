<?php

namespace Database\Seeders;

use App\Models\ChatThread;
use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User;


class ChatThreadsTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $sender) {
            $receiver = $users->where('id', '!=', $sender->id)->random();

            $thread = ChatThread::create([
                'user1_id' => $sender->id,
                'user2_id' => $receiver->id,
                'last_message' => 'Hello! Are you available for service?',
                'updated_at' => now(),
            ]);

            Message::create([
                'chat_thread_id' => $thread->id,
                'sender_id' => $sender->id,
                'message' => 'Hello! Are you available for service?',
                'type' => 'text',
            ]);
        }
    }
}

