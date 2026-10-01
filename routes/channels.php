<?php
// routes/channels.php
use App\Models\AdminUser;
use App\Models\ProjectTask;

Broadcast::channel('task.{prt_id}', function (AdminUser $user, int $taskId) {
    $isAssigner = $isAssignee = false;
    $task = ProjectTask::with('projectTaskAssignments')->find($taskId);

    if (!$task) {
        return false;
    }
    if (!is_admin()) {
        $checked_user_id = get_admin_user_data($user->adm_id)['adm_emp_id'];
        $isAssigner = $task->projectTaskAssignments->contains(function ($assignment) use ($checked_user_id) {
            return $assignment->pta_assigned_by == $checked_user_id;
        });

        $isAssignee = $task->projectTaskAssignments->contains(function ($assignment) use ($checked_user_id) {
            return $assignment->pta_assign_to == $checked_user_id;
        });
    }
    return $isAssigner || $isAssignee || is_admin();
});
