<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller {
    public function store(Request $request, $task_id) {
        $user = auth()->user();
        $isAssigner = false;
        $isAssignee = false;

        $task = ProjectTask::with('projectTaskAssignments')->findOrFail($task_id);

        // Verify task permissions
        if (!is_admin()) {
            $checked_user_id = get_admin_user_data($user->adm_id)['adm_emp_id'];
            $isAssigner = $task->projectTaskAssignments->contains(
                function ($assignment) use ($checked_user_id) {
                    return $assignment->pta_assign_by == $checked_user_id;
                }
            );

            $isAssignee = $task->projectTaskAssignments->contains(
                function ($assignment) use ($checked_user_id) {
                    return $assignment->pta_assign_to == $checked_user_id;
                }
            );
        }

        if (!$isAssigner && !$isAssignee && !is_admin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'task_comment' => 'required|string|max:2000',
        ]);

        TaskComment::query()->create([
            'prt_id' => $task->prt_id,
            'user_id' => $user->adm_id,
            'comment' => $request->task_comment,
        ]);
        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
