<?php

use App\Models\Employee;
use App\Services\PermissionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

function get_logged_in_user_employee_data() {
    if (!Auth::check()) {
        return null;
    }

    return Employee::find(Auth::user()->adm_emp_id);
}

function get_employee_data($emp_id) {
    return Employee::find($emp_id);
}

function get_logged_in_user_id() {
    return Auth::id();
}

function get_logged_in_adm_name() {
    if (!Auth::check()) {
        return null;
    }
    return Auth::user()->adm_name;
}

function get_logged_in_adm_pic() {
    return Auth::user()?->adm_pic;
}

function get_logged_in_user_emp_id() {
    return get_logged_in_user_employee_data()?->emp_id;
}

function get_logged_in_user_emp_name() {
    return get_logged_in_user_employee_data()?->emp_full_name;
}

function get_logged_in_user_emp_internal_id() {
    return get_logged_in_user_employee_data()?->emp_internal_id;
}

function get_logged_in_user_emp_department() {
    return get_logged_in_user_employee_data()?->emp_department;
}

function get_logged_in_user_emp_sub_department() {
    return get_logged_in_user_employee_data()?->emp_sub_department;
}

function get_logged_in_user_emp_status() {
    return get_logged_in_user_employee_data()?->emp_status;
}

function get_logged_in_user_emp_profile_pic() {
    return get_logged_in_user_employee_data()?->emp_photo;
}

function is_admin() {
    return get_logged_in_user_role() == 'admin';
}

function get_logged_in_user_role() {
    return Auth::user()?->adm_role;
}

function get_logged_in_emp_designation() {
    $designation = 'admin';
    if (!is_admin()) {
        $designation = get_employee_data(get_logged_in_user_emp_id())['emp_designation'];
    }
    return $designation;
}

if (!function_exists('getEmployeeChildren')) {

    /**
     * Get children of an employee up to a specified depth.
     *
     * @param int $employeeId
     * @param bool $onlyIds
     * @param int|null $depth
     * @return array
     */
    function get_employee_children_in_depth(int $employeeId, bool $onlyIds = false, ?int $depth = 1): array {
        $employees = Employee::query()->select('emp_id', 'emp_internal_id', 'emp_full_name', 'emp_email', 'emp_designation', 'emp_department', 'emp_sub_department', 'emp_joining_date', 'emp_status', 'emp_photo', 'emp_reporting_to')->get()->groupBy('emp_reporting_to');
        $result = [];
        $findChildren = function (int $parentId, int $currentDepth) use (&$findChildren, &$result, $employees, $onlyIds, $depth) {
            // Stop when depth limit is reached
            if ($depth !== null && $currentDepth > $depth) {
                return;
            }

            foreach ($employees->get($parentId, collect()) as $employee) {
                $result[] = $onlyIds ? $employee->emp_id : $employee->toArray();
                $findChildren($employee->emp_id, $currentDepth + 1);
            }
        };
        $findChildren($employeeId, 1);
        return $result;
    }
}

if (!function_exists('permission_service')) {
    function permission_service(): PermissionService {
        return app(PermissionService::class);
    }
}


if (!function_exists('get_all_permissions')) {
    function get_all_permissions(): array {
        return permission_service()->all();
    }
}


if (!function_exists('permission')) {
    function permission(string $module, ?string $action = null): bool {
        if ($action === null) {
            return permission_service()->hasModule($module);
        }
        return permission_service()->can($module, $action);
    }
}


if (!function_exists('permission_can')) {
    function permission_can(string $module, string $action): bool {
        return permission_service()->can($module, $action);
    }
}


if (!function_exists('permission_route')) {
    function permission_route(string $module, array $parameters = [], bool $absolute = true): ?string {
        return permission_service()->route($module, $parameters, $absolute);
    }
}


if (!function_exists('permission_route_name')) {
    function permission_route_name(string $module): ?string {
        return permission_service()->routeName($module);
    }
}


if (!function_exists('permission_module')) {
    function permission_module(string $module): ?array {
        return permission_service()->module($module);
    }
}


if (!function_exists('logged_in_user_role')) {
    function logged_in_user_role(): ?string {
        return permission_service()->role();
    }
}

if (!function_exists('user_online_status')) {
    function user_online_status($adm_id): array {
        $details = \App\Models\AdminUser::where('adm_id', '=', $adm_id)->first();

        // Panel is currently active
        $isPanelActive = !empty($details->adm_panel_active)
            && !empty($details->adm_panel_last_seen_at)
            && Carbon::parse($details->adm_panel_last_seen_at)->gt(now()->subSeconds(PANEL_ACTIVE_DURATION));

        if ($isPanelActive) {
            return [
                'status' => 'online',
                'label' => 'Online',
                'bg' => '#ecfdf5',
                'text' => '#047857',
                'border' => '#a7f3d0',
                'dot' => '#10b981',
                'pulse' => true,
            ];
        }

        // User is logged in but panel is not active
        $isLoggedIn = !empty($details->adm_last_activity_at)
            && Carbon::parse($details->adm_last_activity_at)->gt(now()->subMinutes(IS_LOGGED_CHECK_DURATION));

        if ($isLoggedIn) {
            return [
                'status' => 'logged_in',
                'label' => 'Logged In',
                'bg' => '#f0f9ff',
                'text' => '#0369a1',
                'border' => '#bae6fd',
                'dot' => '#0284c7',
                'pulse' => false,
            ];
        }

        // Offline
        return [
            'status' => 'offline',
            'label' => 'Offline',
            'bg' => '#f8fafc',
            'text' => '#64748b',
            'border' => '#e2e8f0',
            'dot' => '#94a3b8',
            'pulse' => false,
        ];
    }
}

if (!function_exists('user_online_status_badge')) {
    function user_online_status_badge($adm_id): string {
        $s = user_online_status($adm_id);

        return sprintf(
            '<span class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background-color: %s; color: %s; border: 1px solid %s; font-size: 12px; font-weight: 500;">' .
            '<span style="width: 7px; height: 7px; border-radius: 50%%; background-color: %s;"></span>' .
            '<span>%s</span>' .
            '</span>',
            $s['bg'],
            $s['text'],
            $s['border'],
            $s['dot'],
            e($s['label'])
        );
    }
}

if (!function_exists('user_online_status_dot')) {
    function user_online_status_dot($adm_id, string $customStyle = 'bottom: 7px; right: 11px;'): string {
        $s = user_online_status($adm_id);

        $pulseHtml = $s['pulse'] ? sprintf('<span class="status-pulse-ring" style="background-color: %s;"></span>', $s['dot']) : '';

        $style = sprintf('width: 16px; height: 16px; background-color: %s; %s', $s['dot'], $customStyle);
        return sprintf(
            '<span class="position-absolute rounded-circle border border-2 border-white" ' .
            'style="%s" ' .
            'title="%s">%s</span>',
            $style,
            e($s['label']),
            $pulseHtml
        );
    }
}
