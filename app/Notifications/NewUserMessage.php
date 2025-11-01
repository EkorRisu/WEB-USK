<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Message;
use App\Models\User;

class NewUserMessage extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $sender;

    public function __construct(Message $message, User $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    public function via(object $notifiable): array
    {
        return ['database']; 
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'sender_id' => $this->sender->id, // PENTING: Untuk filtering di Controller
            'sender_name' => $this->sender->name,
            'message_preview' => \Str::limit($this->message->message, 50),
            'chat_route' => route('user.chat', ['user_id' => $this->sender->id]),
        ];
    }
}