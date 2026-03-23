<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Conversation;
use App\Models\Message;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected $conversation;
    protected $message;

    public function __construct(Conversation $conversation, Message $message)
    {
        $this->conversation = $conversation;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'product_title' => $this->conversation->product->title,
            'sender_name' => $this->message->sender->name,
            'message_content' => $this->message->content,
        ];
    }
}