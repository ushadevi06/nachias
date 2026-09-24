<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatHistory extends Model
{
    use HasFactory;

    protected $table = 'chat_histories';

    protected $fillable = [
        'user_id',
        'session_id',
        'title',
        'messages',
        'conversation_history',
    ];

    protected $casts = [
        'messages' => 'array',
        'conversation_history' => 'array',
    ];

    /**
     * Get the user that owns the chat history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
