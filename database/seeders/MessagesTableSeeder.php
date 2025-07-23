<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\ChatThread;
use App\Models\User;


class MessagesTableSeeder extends Seeder
{
    public function run()
    {
        $threads = ChatThread::all();

        foreach ($threads as $thread) {
            $sender = User::find($thread->user1_id);

            Message::create([
                'chat_thread_id' => $thread->id,
                'sender_id' => $sender->id,
                'message' => 'Hello, I need an electrician tomorrow.',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

