<?php

namespace App\Http\Controllers;

use App\Enums\DesignationEnum;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Enums\UserRoleEnum;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\ProjectWithTaskActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller {
    public function index(Request $request) {
        $loggedInUser = get_logged_in_user_emp_id();

        $allProjectQuery = Project::query();
        $allProjectQuery->with('tasks');
        if (!is_admin()) {
            $allProjectQuery
                ->where('pro_status', ProjectStatus::ACTIVE->value)
                ->where(function ($query) use ($loggedInUser) {
                    // User is the project manager
                    $query->where('pro_manager', $loggedInUser)
                        // OR user is assigned to a task of this project
                        ->orWhereExists(function ($subQuery) use ($loggedInUser) {
                            $subQuery->select(DB::raw(1))
                                ->from('project_tasks')
                                ->join('project_task_assignments', 'project_tasks.prt_id', '=', 'project_task_assignments.pta_prt_id')
                                ->whereColumn('project_tasks.prt_pro_id', 'projects.pro_id')
                                ->where('project_task_assignments.pta_assign_to', $loggedInUser);
                        });

                    if (get_logged_in_user_role() === UserRoleEnum::MANAGER->value) {
                        $teamMembers = get_employee_children_in_depth($loggedInUser);
                        if (!empty($teamMembers)) {
                            $teamMemberIds = array_column($teamMembers, 'emp_id');

                            if (get_logged_in_emp_designation() == DesignationEnum::MANAGER) {
                                $query->orWhereIn('pro_manager', $teamMemberIds);
                            }
                            if (get_logged_in_emp_designation() == DesignationEnum::TL) {
                                $query->orWhereExists(function ($subQuery) use ($teamMemberIds) {
                                    $subQuery->select(DB::raw(1))->from('project_tasks')
                                        ->join('project_task_assignments', 'project_tasks.prt_id', '=', 'project_task_assignments.pta_prt_id')
                                        ->whereColumn('project_tasks.prt_pro_id', 'projects.pro_id')
                                        ->whereIn('project_task_assignments.pta_assign_to', $teamMemberIds);
                                });
                            }
                        }
                    }
                });
        }
        $all_projects = $allProjectQuery->paginate(config('constants.PER_PAGE_ITEM_COUNT'), pageName: "all_projects")->withQueryString();
        return view('projects.all-projects', compact('all_projects'));
    }

    public function addProject() {
        $mode = 'add';
        return view('projects.add-edit-project', compact('mode'));
    }

    public function viewProject($pro_id) {
        $pro_id = my_decrypt($pro_id);
        $project_manager = $project_team = $project_task_assignee = [];
        $prefix = config('constants.TABLE_PREFIX');

        if ($this->getAllProjectExistingMembers($pro_id, get_logged_in_user_emp_id()) || is_admin()) {
            $project_data = Project::query()->where('pro_id', '=', $pro_id)->first()?->toArray();

            if (!empty($project_data)) {
                // 1. Build the base query
                $projectTaskQuery = ProjectTask::query()
                    ->with('projectTaskAssignments')
                    ->select('project_tasks.*', DB::raw('(
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
            ) AS assignees'))
                    ->where('prt_pro_id', '=', $pro_id);

                // 2. Conditionally filter if the logged-in user is an employee
                if (get_logged_in_user_role() === 'employee') {
                    $loggedInUserId = get_logged_in_user_emp_id();
                    $projectTaskQuery->whereIn('prt_id', function ($subQuery) use ($loggedInUserId) {
                        $subQuery->select('pta_prt_id')
                            ->from('project_task_assignments')
                            ->where('pta_assign_to', '=', $loggedInUserId);
                    });
                }
                $project_tasks = $projectTaskQuery->paginate(config('constants.PER_PAGE_ITEM_COUNT'), pageName: 'project_tasks')->withQueryString();

                if (count($project_tasks) > 0) {
                    $project_task_assignee = $project_tasks->getCollection()
                        ->flatMap(function ($task) {
                            return $task->projectTaskAssignments->pluck('pta_assign_to');
                        })->unique()->values()->toArray();
                }

                $project_team = ProjectTask::query()->select('pta_assign_to as team_member_emp_id', 'employees.emp_full_name', 'employees.emp_designation', 'employees.emp_photo')->join('project_task_assignments', 'project_tasks.prt_id', '=', 'project_task_assignments.pta_prt_id')->join('employees', 'employees.emp_id', '=', 'project_task_assignments.pta_assign_to')->where([['prt_pro_id', '=', $pro_id], ['project_task_assignments.pta_assign_to', '!=', $project_data['pro_manager']]])->distinct('employees.emp_id')->paginate(config('constants.PER_PAGE_ITEM_COUNT'), pageName: 'project_teams')->withQueryString();

                if (!empty($project_data['pro_manager'])) {
                    $project_manager = get_employee_data($project_data['pro_manager']);
                }

                $completed_tasks = count(($project_tasks->where('prt_status', '=', TaskStatus::COMPLETED->value)));
                $pending_tasks = count($project_tasks) - $completed_tasks;

                $task_statistics = [
                    'completed_tasks' => $completed_tasks,
                    'pending_tasks' => $pending_tasks,
                ];

                return view('projects.view-project', compact('project_data', 'project_manager', 'pro_id', 'project_tasks', 'project_team', 'task_statistics', 'project_task_assignee'));
            } else {
                abort('404');
            }
        } else {
            abort('403');
        }
    }

    public function getAllProjectExistingMembers($pro_id, $emp_id) {
        $is_project_manager = Project::query()->where([
            ['pro_manager', '=', $emp_id],
            ['pro_id', '=', $pro_id],
        ])->exists();

        $is_any_task_assigned = Project::query()->join('project_tasks', 'prt_pro_id', '=', 'pro_id')->join('project_task_assignments', 'prt_id', '=', 'pta_prt_id')->where('prt_pro_id', '=', $pro_id)->where(function ($query) use ($emp_id) {
            $query->where('pta_assign_by', '=', $emp_id)
                ->orWhere('pta_assign_to', '=', $emp_id);
        })->exists();

        return $is_project_manager || $is_any_task_assigned;
    }

    public function saveProject(Request $request) {
        $request->validate([
            'project_name' => 'required|min:' . MIN_LENGTH . '|max:' . MAX_LENGTH_100,
            'project_desc' => 'nullable|min:' . MIN_LENGTH_10,
            'status' => 'required',
            'project_deadline' => 'required|date',
        ]);
        try {
            DB::beginTransaction();

            $loggedInUser = get_logged_in_user_emp_id();

            $project = new Project();
            $project->pro_name = $request->project_name;
            $project->pro_status = $request->status;
            $project->pro_manager = get_logged_in_user_emp_id();
            $project->pro_deadline = $request->project_deadline;
            $project->pro_description = $request->project_desc ?? null;
            $project->pro_created_by = setCreatedUpdatedBy();
            $project->pro_created_on = date(config('constants.DB_DATE_TIME_FORMAT'));

            if ($project->save()) {
                /*ProjectWithTaskActivityLog::init()
                    ->project($project->pro_id)
                    ->slug('project.created')
                    ->data([
                        'name' => $project->pro_name,
                        'user' => get_employee_data($loggedInUser)['emp_full_name'],
                    ])
                    ->performedBy($loggedInUser)
                    ->log();*/

                DB::commit();
                return redirect()->route('projects.list', $request->query())->with('success', 'Project added successfully.');
            } else {
                return redirect()->route('projects.list', $request->query())->with('error', 'Something went wrong.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('projects.list', $request->query())->with('error', 'Something went wrong.');
        }
    }

    public function updateProjectStatus(Request $request, $pro_id) {
        $pro_id = my_decrypt($pro_id);
        $loggedInUser = get_logged_in_user_emp_id();

        $projectExist = Project::query()->where('pro_id', '=', $pro_id)->first();
        if (!empty($projectExist)) {
            $old_status = $projectExist->pro_status->label();

            $projectExist->pro_status = $request->status;
            $projectExist->pro_updated_by = setCreatedUpdatedBy();
            $projectExist->pro_updated_on = date(config('constants.DB_DATE_TIME_FORMAT'));
            $is_updated = $projectExist->save();
            if (!$is_updated) {
                return response()->json(['status' => false, 'message' => 'Project status not updated.',], 500);
            }
            /*ProjectWithTaskActivityLog::init()
                ->project($pro_id)
                ->slug('project.status.updated')
                ->data([
                    'old_status' => $old_status,
                    'new_status' => ProjectStatus::tryFrom($request->status)->label(),
                    'user' => get_employee_data($loggedInUser)['emp_full_name'],
                ])
                ->performedBy($loggedInUser)
                ->log();*/
            return response()->json(['status' => true, 'message' => 'Project status updated successfully.'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Project not found.',], 404);
        }
    }
}
