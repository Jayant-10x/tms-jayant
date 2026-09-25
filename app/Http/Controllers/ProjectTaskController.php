<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\ProjectTask;
use App\Models\ProjectTaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectTaskController extends Controller {

    public function allTasks() {
        $prefix = config('constants.TABLE_PREFIX');

        $projectTaskQuery = ProjectTask::query()
            ->with('project')
            ->select(
                'project_tasks.*',
                DB::raw('(
            SELECT GROUP_CONCAT(
                DISTINCT ' . $prefix . 'employees.emp_full_name
                ORDER BY ' . $prefix . 'employees.emp_full_name
                SEPARATOR ", "
            )
            FROM ' . $prefix . 'project_task_assignments
            INNER JOIN ' . $prefix . 'employees
                ON ' . $prefix . 'employees.emp_id = ' . $prefix . 'project_task_assignments.pta_assign_to
            WHERE ' . $prefix . 'project_task_assignments.pta_prt_id = ' . $prefix . 'project_tasks.prt_id
        ) AS assignees'));
        if (!is_admin()) {
            $loggedInEmpId = get_logged_in_user_emp_id();

            $projectTaskQuery
                ->whereHas('project', function ($query) {
                    $query->where('pro_status', '=', ProjectStatus::ACTIVE->value);
                })
                ->whereHas('projectTaskAssignments', function ($query) use ($loggedInEmpId) {
                    $query->where(function ($q) use ($loggedInEmpId) {
                        $q->where('pta_assign_by', $loggedInEmpId)
                            ->orWhere('pta_assign_to', $loggedInEmpId);
                    });
                });
        }
        $project_tasks = $projectTaskQuery->paginate(config('constants.PER_PAGE_ITEM_COUNT'))->withQueryString();
        return view('project-task.all-tasks', compact('project_tasks'));
    }

    public function assignedToMe(Request $request) {
        $all_assigned_to_me = null;

        if (!is_admin()) {
            $loggedInEmpId = get_logged_in_user_emp_id();

            $all_assigned_to_me = ProjectTask::query()
                ->with('project')
                ->join('project_task_assignments', 'project_tasks.prt_id', '=', 'project_task_assignments.pta_prt_id')
                ->join('employees', 'employees.emp_id', '=', 'project_task_assignments.pta_assign_by')
                ->whereHas('project', function ($query) {
                    $query->where('pro_status', '=', ProjectStatus::ACTIVE->value);
                })
                ->where('pta_assign_to', $loggedInEmpId)->paginate(config('constants.PER_PAGE_ITEM_COUNT'))->withQueryString();
        }
        return view('project-task.all-my-tasks', compact('all_assigned_to_me'));
    }

    public function assignedByMe(Request $request) {
        $all_assigned_by_me = null;
        $prefix = config('constants.TABLE_PREFIX');


        if (!is_admin()) {
            $loggedInEmpId = get_logged_in_user_emp_id();
            $projectTaskQuery = ProjectTask::query()
                ->with('project')
                ->select(
                    'project_tasks.*',
                    DB::raw('(
                    SELECT GROUP_CONCAT(
                        DISTINCT ' . $prefix . 'employees.emp_full_name
                        ORDER BY ' . $prefix . 'employees.emp_full_name
                        SEPARATOR ", "
                    )
                    FROM ' . $prefix . 'project_task_assignments
                    INNER JOIN ' . $prefix . 'employees
                        ON ' . $prefix . 'employees.emp_id =
                           ' . $prefix . 'project_task_assignments.pta_assign_to
                    WHERE ' . $prefix . 'project_task_assignments.pta_prt_id =
                          ' . $prefix . 'project_tasks.prt_id
                ) AS assignees')
                )
                ->whereHas('project', function ($query) {
                    $query->where('pro_status', '=', ProjectStatus::ACTIVE->value);
                })
                ->whereHas('projectTaskAssignments', function ($query) use ($loggedInEmpId) {
                    $query->where(function ($q) use ($loggedInEmpId) {
                        $q->where('pta_assign_by', $loggedInEmpId);
                    });
                });
            $all_assigned_by_me = $projectTaskQuery->paginate(config('constants.PER_PAGE_ITEM_COUNT'))->withQueryString();
        }
        return view('project-task.all-assigned-tasks', compact('all_assigned_by_me'));
    }

    public function teamTasks(Request $request) {
        $prefix = config('constants.TABLE_PREFIX');

        $projectTaskQuery = ProjectTask::query()
            ->with('project')
            ->select(
                'project_tasks.*',
                DB::raw('(
                SELECT GROUP_CONCAT(
                    DISTINCT ' . $prefix . 'employees.emp_full_name
                    ORDER BY ' . $prefix . 'employees.emp_full_name
                    SEPARATOR ", "
                )
                FROM ' . $prefix . 'project_task_assignments
                INNER JOIN ' . $prefix . 'employees
                    ON ' . $prefix . 'employees.emp_id =
                       ' . $prefix . 'project_task_assignments.pta_assign_to
                WHERE ' . $prefix . 'project_task_assignments.pta_prt_id =
                      ' . $prefix . 'project_tasks.prt_id
            ) AS assignees')
            );
        if (!is_admin()) {
            $loggedInEmpId = get_logged_in_user_emp_id();
            $team_members = get_employee_children_in_depth((int)$loggedInEmpId, true, config('constants.DEFAULT_DEPTH'));

            $projectTaskQuery
                ->whereHas('project', function ($query) {
                    $query->where('pro_status', '=', ProjectStatus::ACTIVE->value);
                })
                ->whereHas('projectTaskAssignments', function ($query) use ($team_members) {
                    $query->whereIn('pta_assign_to', $team_members);
                });
        }
        $project_tasks = $projectTaskQuery->paginate(config('constants.PER_PAGE_ITEM_COUNT'))->withQueryString();

        return view('project-task.all-team-tasks', compact('project_tasks'));
    }

    public function myTasks(Request $request) {
        $my_tasks = null;
        $task_statistics = $tasks_by_status = $calendar_tasks = [];

        if (!is_admin()) {
            $loggedInEmpId = get_logged_in_user_emp_id();

            // Base query reusable across paginated list, counts, and status grouping
            $baseQuery = ProjectTask::query()
                ->select('project_tasks.*') // Avoid column name collisions from joins
                ->with('project')
                ->join('project_task_assignments', 'project_tasks.prt_id', '=', 'project_task_assignments.pta_prt_id')
                ->join('employees', 'employees.emp_id', '=', 'project_task_assignments.pta_assign_by')
                ->whereHas('project', function ($query) {
                    $query->where('pro_status', '=', ProjectStatus::ACTIVE->value);
                })
                ->where('pta_assign_to', $loggedInEmpId);

            $my_tasks = (clone $baseQuery)->paginate(config('constants.PER_PAGE_ITEM_COUNT'))->withQueryString();
            $calendar_tasks = $calendar_tasks = (clone $baseQuery)
                ->get()
                ->map(function ($task) {
                    $task->prt_enc_id = my_encrypt($task->prt_id);
                    return $task;
                })
                ->groupBy('prt_due_date')
                ->toArray();;

            // sub-task count
            $all_tasks = (clone $baseQuery)->with('subTasks')->get();
            $tasks_by_status = $all_tasks->groupBy('prt_status');

            $task_statistics = $this->userTaskStatistics([$loggedInEmpId]);

            $today_tasks = $all_tasks->where('prt_due_date', '=', date('Y-m-d'))->where('prt_status', '!=', TaskStatus::COMPLETED->value)->count();
            $upcoming_tasks = $all_tasks->where('prt_due_date', '>', date('Y-m-d'))->where('prt_status', '!=', TaskStatus::COMPLETED->value)->count();
            $overdue_tasks = $all_tasks->where('prt_due_date', '<', date('Y-m-d'))->where('prt_status', '!=', TaskStatus::COMPLETED->value)->count();

            if (isset($task_statistics[$loggedInEmpId])) {
                $task_statistics[$loggedInEmpId] += [
                    'today_tasks' => $today_tasks,
                    'upcoming_tasks' => $upcoming_tasks,
                    'overdue_tasks' => $overdue_tasks,
                ];
            }
        }
        return view('my-tasks.my-tasks', compact('my_tasks', 'task_statistics', 'tasks_by_status', 'calendar_tasks'));
    }

    public function store(Request $request) {
        $request->merge([
            'task_tags_hid' => $request->has('task_tags_hid') ? json_decode($request->task_tags_hid, true) : null,
            'sub_task_hid' => $request->has('sub_task_hid') ? json_decode($request->sub_task_hid, true) : null,
        ]);

        $request->validate([
            'task_title' => 'required|min:' . MIN_LENGTH_10 . '|max:' . MAX_LENGTH,
            'project' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'status' => 'required',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'estimated_hours' => 'nullable|numeric',
            'assign_to' => 'required|array|min:1',
            'assign_to.*' => 'required|exists:employees,emp_id',
            'task_tags_hid' => 'nullable|array|min:1',
            'task_tags_hid.*' => 'string|min:' . MIN_LENGTH . '|max:' . MAX_LENGTH_50,
            'sub_task_hid' => 'nullable|array|min:1',
            'sub_task_hid.*' => 'string|min:' . MIN_LENGTH . '|max:' . MAX_LENGTH,
            'desc' => 'nullable|string|min:' . MIN_LENGTH_10 . '|max:' . MAX_LENGTH_2000,
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|mimes:jpeg,jpg,png,pdf|max:3072', // restricted to 3MB
        ]);

        $current_date = date(config('constants.DB_DATE_TIME_FORMAT'));
        $fileNames = [];

        try {
            DB::beginTransaction();
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $originalNameOnly = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = $originalNameOnly . '_' . time() . '.' . $extension;
                    $file->storeAs('task_attachments', $uniqueName, 'public');
                    $fileNames[] = $uniqueName;
                }
            }

            $project_task = new ProjectTask();
            $project_task->prt_title = $request->task_title;
            $project_task->prt_pro_id = my_decrypt($request->project);
            $project_task->prt_category = $request->category;
            $project_task->prt_priority = $request->priority;
            $project_task->prt_status = $request->status;
            $project_task->prt_start_date = $request->start_date;
            $project_task->prt_due_date = $request->due_date;
            $project_task->prt_est_hours = $request->estimated_hours;
            $project_task->prt_tags = $request->task_tags_hid ?? [];
            $project_task->prt_attachments = $fileNames;
            $project_task->prt_description = $request->desc;
            $project_task->prt_created_by = setCreatedUpdatedBy();
            $project_task->prt_created_on = $current_date;
            $project_task->save();

            if (!empty($project_task->prt_id) && !empty($request->sub_task_hid)) {
                $request->merge([
                    'sub_tasks' => $request->sub_task_hid
                ]);
                (new ProjectSubTaskController())->storeSubTaskBulk($request, $project_task->prt_id);
            }

            if (!empty($project_task->prt_id) && !empty($request->assign_to)) {
                $task_assignees = $request->assign_to;
                $this->taskAssignment($project_task->prt_id, $task_assignees);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Task added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong.');
        } catch (\Throwable $e) {
            DB::rollBack(); // ADDED: Missed a rollback statement in your original second catch block
            return redirect()->back()->with('error', 'Something went wrong in transaction.');
        }
    }

    public function viewTask($prt_id, $called_from = null) {
        $prt_id = my_decrypt($prt_id);
        $task_data = ProjectTask::query()->with(['subTasks:pst_id,pst_prt_id,pst_title,pst_is_done', 'projectTaskAssignments:pta_prt_id,pta_assign_by,pta_assign_to', 'project:pro_id,pro_name', 'projectTaskAssignments.projectTaskAssignTo:emp_id,emp_full_name', 'projectTaskAssignments.projectTaskAssignedBy:emp_id,emp_full_name'])->where('prt_id', '=', $prt_id)->first();

        $task_assignees = [];
        $task_assigned_by = [];
        if (!empty($task_data->projectTaskAssignments)) {
            foreach ($task_data->projectTaskAssignments as $task_assignment) {
                if (!in_array($task_assignment->projectTaskAssignTo->emp_id, array_keys($task_assignees))) {
                    $task_assignees[$task_assignment->projectTaskAssignTo->emp_id] = $task_assignment->projectTaskAssignTo->emp_full_name;
                }
                if (count($task_assigned_by) == 0) {
                    $task_assigned_by[$task_assignment->projectTaskAssignedBy->emp_id] = $task_assignment->projectTaskAssignedBy->emp_full_name;
                }
            }
        }
        return view('project-task.view-task', compact('prt_id', 'task_data', 'task_assignees', 'task_assigned_by', 'called_from'));
    }

    public function updateTaskStatus(Request $request, $prt_id) {
        $prt_id = my_decrypt($prt_id);
        $taskExist = ProjectTask::query()->where('prt_id', '=', $prt_id)->first();
        if (!empty($taskExist)) {
            $taskExist->prt_status = $request->status;
            $is_updated = $taskExist->save();
            if (!$is_updated) {
                return response()->json(['status' => false, 'message' => 'Task status not updated.',], 500);
            }
            return response()->json(['status' => true, 'message' => 'Task status updated successfully.'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Task not found.',], 404);
        }
    }

    public function uploadTaskAttachment(Request $request, $prt_id) {
        $prt_id = my_decrypt($prt_id);

        $task = ProjectTask::query()->where('prt_id', $prt_id)->first();

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found.',
            ], 404);
        }

        if (!empty($task->prt_attachments) && count($task->prt_attachments) > 10) {
            return response()->json([
                'status' => false,
                'message' => 'Maximum number of attachments exceeded.',
            ],);
        }

        $request->validate([
            'task_attachment' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:3072', // 3MB
            ],
        ]);

        $file = $request->file('task_attachment');

        $originalNameOnly = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $fileName = $originalNameOnly . '_' . time() . '.' . $extension;
        $file->storeAs('task_attachments', $fileName, 'public');

        $attachments = $task->prt_attachments ?? [];
        if (!is_array($attachments)) {
            $attachments = [];
        }
        $attachments[] = $fileName;
        $task->prt_attachments = $attachments;
        $task->save();

        return response()->json([
            'status' => true,
            'message' => 'File uploaded successfully.',
            'data' => [
                'file_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'url' => asset('storage/task_attachments/' . $fileName),
            ],
        ]);
    }

    private function taskAssignment($prt_id, $assignees) {
        if (!empty($assignees) && $prt_id) {
            $assignees_arr = [];

            $created_by = setCreatedUpdatedBy();
            $assigned_by = get_logged_in_user_emp_id();
            $current_date = date(config('constants.DB_DATE_TIME_FORMAT'));
            foreach ($assignees as $assignee) {
                $assignees_arr[] = [
                    'pta_prt_id' => $prt_id,
                    'pta_assign_by' => $assigned_by,
                    'pta_assign_to' => $assignee,
                    'pta_created_by' => $created_by,
                    'pta_created_on' => $current_date,
                ];
            }
            if (!empty($assignees_arr)) {
                ProjectTaskAssignment::query()->insert($assignees_arr);
            }
            return true;
        }
        return false;
    }

    public function userTaskStatistics($emp_ids, $pro_id = null) {
        $userTaskQuery = ProjectTask::query()->join('project_task_assignments', 'prt_id', '=', 'pta_prt_id');

        if (!empty($pro_id)) {
            $userTaskQuery->join('projects', 'prt_pro_id', '=', 'pro_id')->where('prt_pro_id', $pro_id);
        }

        $userTaskData = $userTaskQuery->whereIn('pta_assign_to', $emp_ids)->get();
        $statistics = [];
        foreach ($emp_ids as $emp_id) {
            $employeeTasks = $userTaskData->where('pta_assign_to', $emp_id);

            $total_tasks = $employeeTasks->count();
            $completed_tasks = $employeeTasks->where('prt_status', TaskStatus::COMPLETED->value)->count();
            $pending_tasks = $total_tasks - $completed_tasks;

            $statistics[$emp_id] = [
                'total_tasks' => $total_tasks,
                'completed_tasks' => $completed_tasks,
                'pending_tasks' => $pending_tasks,
            ];
        }
        return $statistics;
    }
}
