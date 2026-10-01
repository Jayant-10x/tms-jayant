<?php

namespace App\Events;

use App\Models\TaskComment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCommentSent implements ShouldBroadcastNow {
    use Dispatchable, SerializesModels;

    public $comment;

    public function __construct(TaskComment $comment) {
        // Load user details for frontend rendering (name, avatar, etc.)
        $this->comment = $comment->load('user.employee');
    }

    public function broadcastOn() {
        return new PrivateChannel('task.' . $this->comment->prt_id);
    }

    public function broadcastAs(): string {
        return 'comment.created';
    }
}
