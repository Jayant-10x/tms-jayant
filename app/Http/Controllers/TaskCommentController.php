<?php

namespace App\Http\Controllers;

use App\Events\TaskCommentSent;
use App\Models\ProjectTask;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller {
    public function store(Request $request, $task_id) {
        $user = auth()->user();
        $isAssigner = $isAssignee = false;

        $task = ProjectTask::with('projectTaskAssignments')->findOrFail($task_id);
        if (empty($task)) {
            return response()->json(['message' => 'Task Not Found'], 404);
        }
        // Verify task permissions
        if (!is_admin()) {
            $checked_user_id = get_admin_user_data($user->adm_id)['adm_emp_id'];

            $isAssigner = $task->projectTaskAssignments->contains(function ($assignment) use ($checked_user_id) {
                return $assignment->pta_assigned_by == $checked_user_id;
            });

            $isAssignee = $task->projectTaskAssignments->contains(function ($assignment) use ($checked_user_id) {
                return $assignment->pta_assign_to == $checked_user_id;
            });
        }

        if (!$isAssigner && !$isAssignee && !is_admin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Create comment using task's primary key (prt_id)
        $comment = TaskComment::create([
            'prt_id' => $task->prt_id,
            'user_id' => $user->adm_id,
            'comment' => $request->comment,
        ]);

        // Eager load user and employee relationship before broadcasting
        $comment->load('user.employee');

        // Broadcast to other users on the WebSocket channel
        broadcast(new TaskCommentSent($comment))->toOthers();

        return response()->json($comment);
    }
}
