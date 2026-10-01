<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\ProjectTask;

class DashboardController extends Controller {
    public function index() {
        $user_statistics = [];
        if (!is_admin()) {
            $user_statistics = (new ProjectTaskController())->userTaskStatistics(get_logged_in_user_emp_id());
        } else {
            $user_statistics = $this->allUsersTaskStatistics();
        }
        return view('dashboard', compact('user_statistics'));
    }

    private function allUsersTaskStatistics() {
        $today = date('Y-m-d');

        return ProjectTask::query()
            ->selectRaw('COUNT(*) as total_tasks')
            ->selectRaw('SUM(CASE WHEN prt_status = ? THEN 1 ELSE 0 END) as completed_tasks', [TaskStatus::COMPLETED])
            ->selectRaw('SUM(CASE WHEN prt_status != ? THEN 1 ELSE 0 END) as pending_tasks', [TaskStatus::COMPLETED])
            ->selectRaw('SUM(CASE WHEN prt_status NOT IN (?, ?) AND prt_due_date < ? THEN 1 ELSE 0 END) as overdue_tasks', [
                TaskStatus::COMPLETED, TaskStatus::CANCELLED, $today
            ])
            ->selectRaw('SUM(CASE WHEN prt_status NOT IN (?, ?, ?) AND prt_due_date >= ? THEN 1 ELSE 0 END) as pending_dashboard_count', [
                TaskStatus::COMPLETED, TaskStatus::CANCELLED, TaskStatus::IN_PROGRESS, $today
            ])
            ->selectRaw('SUM(CASE WHEN prt_status = ? THEN 1 ELSE 0 END) as progress_tasks', [TaskStatus::IN_PROGRESS])
            ->first()
            ->toArray();
    }
}
