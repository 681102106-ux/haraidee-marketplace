<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['conversation_id', 'sender_id', 'type', 'content', 'is_read'];

    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

   
}