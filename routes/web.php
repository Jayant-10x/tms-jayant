<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectSubTaskController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\TeamMembersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthenticationController::class, 'login'])
        ->name('login');

    Route::post('/login', [AuthenticationController::class, 'authenticate'])
        ->name('login.authenticate');

});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');

    Route::get('/sub-department/{department}', [AjaxController::class, 'getSubDepartments'])->middleware('permission:employees.add|employees.edit')->name('sub_department_by_department');
    Route::get('/employees-reporting-to', [AjaxController::class, 'getReportingToEmployees'])->middleware('permission:employees.add|employees.edit')->name('reporting_to_employees');
    Route::get('/show-modal-popup/add-edit', [AjaxController::class, 'getAddEditPopUpForms']);
    Route::get('/show-modal-popup/view', [AjaxController::class, 'getViewPopUpsPage']);
    Route::get('/check-unique-emp-internal-id', [AjaxController::class, 'checkUniqueEmpInternalId'])->name('check_unique_emp_internal_id_via_ajax');
    Route::get('/check-unique-emp-email', [AjaxController::class, 'checkUniqueEmpEmail'])->name('check_unique_emp_email_via_ajax');

    Route::get('/notifications/{notification}/handle', [NotificationController::class, 'handle']
    )->name('notifications.handle');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    Route::post('/admin-user/save/{emp_id}', [AdminUserController::class, 'store'])->middleware('permission:admin_users.add')->name('admin_user.save');
    Route::put('/admin-user/update/{adm_id}', [AdminUserController::class, 'update'])->middleware('permission:admin_users.edit')->name('admin_user.update');

    Route::get('/employees', [EmployeeController::class, 'index'])->middleware('permission:employees.view')->name('employees.list');
    Route::get('employee/add', [EmployeeController::class, 'add'])->middleware('permission:employees.add')->name('employees.add');
    Route::post('employee/save', [EmployeeController::class, 'store'])->middleware('permission:employees.add')->name('employees.save');
    Route::get('employee/view/{emp_id}', [EmployeeController::class, 'view'])->middleware('permission:employees.view')->name('employees.view');
    Route::get('employee/edit/{emp_id}', [EmployeeController::class, 'edit'])->middleware('permission:employees.edit')->name('employees.edit');
    Route::put('employee/update/{emp_id}', [EmployeeController::class, 'update'])->middleware('permission:employees.edit')->name('employees.update');

    Route::get('/my-profile', [MyProfileController::class, 'viewProfile'])->name('my_profile');
    Route::get('/my-profile/edit', [MyProfileController::class, 'editProfile'])->name('my_profile.edit');
    Route::put('/my-profile/update/personal-info', [MyProfileController::class, 'updateProfilePersonalInfo'])->name('my_profile.update.personal_info');
    Route::put('/my-profile/update/credentials-info', [MyProfileController::class, 'updateProfileCredentials'])->name('my_profile.update.credentials');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

    Route::get('admin-users', [AdminUserController::class, 'allAdminUsers'])->middleware('permission:admin_users.view')->name('admin_user_module.list');
    Route::get('admin-user/add', [AdminUserController::class, 'addAdmin'])->middleware('permission:admin_users.add')->name('admin_user_module.add');
    Route::post('admin-user/store', [AdminUserController::class, 'saveAdmin'])->middleware('permission:admin_users.add')->name('admin_user_module.store');
    Route::get('admin-user/edit/{adm_id}', [AdminUserController::class, 'editAdmin'])->middleware('permission:admin_users.edit')->name('admin_user_module.edit');
    Route::put('admin-user/update-admin/{adm_id}', [AdminUserController::class, 'updateAdmin'])->middleware('permission:admin_users.edit')->name('admin_user_module.update');
    Route::get('admin-user/view-admin/{adm_id}', [AdminUserController::class, 'viewAdmin'])->middleware('permission:admin_users.view')->name('admin_user_module.view');

    Route::get('team-members', [TeamMembersController::class, 'index'])->middleware('permission:team_members.view')->name('team_members.list');
    Route::get('team-member/view/{called_from}/{member_id}/{pro_id?}', [TeamMembersController::class, 'viewTeamMember'])->middleware('permission:team_members.view')->name('team_member.view');

    Route::get('projects/', [ProjectController::class, 'index'])->middleware('permission:projects.view')->name('projects.list');
    Route::get('project/add', [ProjectController::class, 'addProject'])->middleware('permission:projects.add')->name('projects.add');
    Route::post('project/save', [ProjectController::class, 'saveProject'])->middleware('permission:projects.add')->name('projects.save');
    Route::get('project/view/{pro_id}', [ProjectController::class, 'viewProject'])->middleware('permission:projects.view')->name('project.view');
    Route::post('/update-project-status/{pro_id}', [ProjectController::class, 'updateProjectStatus'])->middleware('permission:projects.edit')->name('update_project_status_via_ajax');


    Route::post('/task/create', [ProjectTaskController::class, 'store'])->middleware('permission:all_tasks.add')->name('task.create');
    Route::get('/all-tasks', [ProjectTaskController::class, 'allTasks'])->middleware('permission:all_tasks.view')->name('all_tasks');
    Route::get('/task/view/{prt_id}/{called_from?}', [ProjectTaskController::class, 'viewTask'])->middleware('permission:all_tasks.view|team_tasks.view|my_tasks.view')->name('task.view');
    Route::post('/update-task-status/{prt_id}', [ProjectTaskController::class, 'updateTaskStatus'])->middleware('permission:all_my_tasks.edit|team_tasks.edit|all_tasks.edit')->name('update_task_status_via_ajax');
    Route::post('/upload-task-attachment/{prt_id}', [ProjectTaskController::class, 'uploadTaskAttachment'])->middleware('permission:all_my_tasks.edit|team_tasks.edit|all_tasks.edit')->name('upload_task_attachments_via_ajax');
    Route::post('/update-sub-task-via-ajax/is-done/{pst_id}', [ProjectSubTaskController::class, 'updateSubTaskIsDone'])->middleware('permission:all_my_tasks.edit|team_tasks.edit|all_tasks.edit')->name('update_sub_task.is_done');
    Route::post('/add-sub-task-via-ajax/{prt_id}', [ProjectSubTaskController::class, 'addSubTask'])->middleware('permission:team_tasks.edit|all_tasks.edit')->name('add_sub_task_via_ajax');
    Route::delete('/delete-sub-task-via-ajax/{pst_id}/{prt_id}', [ProjectSubTaskController::class, 'deleteSubTask'])->middleware('permission:all_tasks.edit')->name('delete_sub_task_via_ajax');

    Route::get('all-my-tasks', [ProjectTaskController::class, 'assignedToMe'])->middleware('permission:all_my_tasks.view|all_my_tasks.edit')->name('all_my_tasks');
    Route::get('all-assigned-tasks', [ProjectTaskController::class, 'assignedByMe'])->middleware('permission:all_assigned_tasks.view|all_assigned_tasks.edit')->name('all_assigned_tasks');
    Route::get('team-tasks', [ProjectTaskController::class, 'teamTasks'])->middleware('permission:team_tasks.view|team_tasks.edit')->name('all_team_tasks');
    Route::get('my-tasks', [ProjectTaskController::class, 'myTasks'])->middleware('permission:my_tasks.view|my_tasks.edit')->name('my_tasks');
});
Route::post('/employee/panel-status', function (Request $request) {
    if (!auth()->check()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }

    auth()->user()->updateQuietly([
        'adm_panel_active' => $request->boolean('active'),
        'adm_panel_last_seen_at' => now(),
        'adm_last_activity_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'active' => $request->boolean('active')
    ]);

// NEw Chnages
//    Another Changes
})->middleware('auth');
