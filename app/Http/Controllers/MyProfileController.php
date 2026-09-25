<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MyProfileController extends Controller {
    public function viewProfile() {
        if (is_admin()) {
            $called_from = 'my_profile';
            $view_file = 'admin-user.view-admin-user';

            $created_updated_arr = ['adm_created_by as created_by', 'adm_created_on as created_on', 'adm_updated_by as updated_by', 'adm_updated_on as updated_on'];
            $emp_data = AdminUser::query()->select(array_merge(['admin_users.*'], $created_updated_arr))->where('adm_id', get_logged_in_user_id())->first();
        } else {
            $emp_id = get_logged_in_user_emp_id();

            $created_updated_arr = ['emp_created_by as created_by', 'emp_created_on as created_on', 'emp_updated_by as updated_by', 'emp_updated_on as updated_on'];
            $emp_data = Employee::query()->select(array_merge(['employees.*'], $created_updated_arr))->with(['reporting_to:emp_full_name,emp_id', 'admin_user_details:adm_emp_id,adm_user_name,adm_role,adm_created_by as created_by,adm_created_on as created_on,adm_updated_by as updated_by,adm_updated_on as updated_on'])->where('emp_id', $emp_id)->first();

            $called_from = 'my_profile';
            $view_file = 'employees.view_employee';
        }
        return view($view_file, compact('emp_data', 'called_from'));
    }

    public function editProfile() {
        if (is_admin()) {
            $called_from = 'my_profile';
            $edit_file = 'admin-user.add-edit-admin-user';
            $mode = 'edit';

            $admin_data = AdminUser::query()->where('adm_id', get_logged_in_user_id())->first();
            $compact_array = [
                'admin_data' => $admin_data,
                'called_from' => $called_from,
                'mode' => $mode,
            ];
        } else {
            $emp_id = get_logged_in_user_emp_id();
            $emp_data = Employee::query()->select('emp_full_name', 'emp_phone_number', 'emp_email', 'emp_photo')->where('emp_id', $emp_id)->first();
            $admin_data = AdminUser::query()->select('adm_user_name')->where('adm_emp_id', $emp_id)->first();

            $called_from = 'my_profile';
            $edit_file = 'my-profile.edit-profile';

            $compact_array = [
                'emp_data' => $emp_data,
                'admin_data' => $admin_data,
                'called_from' => $called_from,
            ];
        }
        return view($edit_file, $compact_array);
    }

    public function updateProfilePersonalInfo(Request $request) {
        $emp_id = get_logged_in_user_emp_id();
        $request->validate([
            'full_name' => 'required|min:' . MIN_LENGTH . '|max:' . MAX_LENGTH_100,
            'phone_number' => 'nullable|numeric',
            'email' => 'required|email',
            'employee_photo' => 'nullable|file|mimes:jpeg,jpg,png|max:5120',
        ]);

        $current_date = date(config('constants.DB_DATE_TIME_FORMAT'));

        $employee_exist = Employee::query()->where('emp_id', $emp_id)->first();
        if (!empty($employee_exist)) {
            $employee_exist->emp_full_name = $request->full_name;
            $employee_exist->emp_phone_number = $request->phone_number;
            $employee_exist->emp_email = $request->email;
            $employee_exist->emp_updated_by = setCreatedUpdatedBy();
            $employee_exist->emp_updated_on = $current_date;

            if ($request->hasFile('profile_photo')) {
                // Delete old photo
                if (!empty($employee->emp_photo)) {
                    Storage::disk('public')->delete(
                        'employees/' . $employee->emp_photo
                    );
                }
                $file = $request->file('profile_photo');

                $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $employee_exist->emp_photo = $filename;
                $file->storeAs('employees', $filename, 'public');
            }

            if ($employee_exist->save()) {
                return redirect()->back()->with('success', 'Personal Info updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Something went wrong.');
            }
        } else {
            return redirect()->back()->with('error', 'Profile not found.');
        }
    }

    public function updateProfileCredentials(Request $request) {
        $emp_id = get_logged_in_user_emp_id();
        $request->validate([
            'user_name' => ['required', 'string', 'min:' . MIN_LENGTH, 'max:' . MAX_LENGTH_100],
            'password' => ['nullable', 'min:8', 'max:' . MAX_LENGTH_20],
        ]);

        $current_date = date(config('constants.DB_DATE_TIME_FORMAT'));

        $admin_user_exist = AdminUser::query()->where('adm_emp_id', $emp_id)->first();
        if (!empty($admin_user_exist)) {
            $admin_user_exist->adm_user_name = $request->user_name;
            if (!empty($request->password)) {
                $admin_user_exist->adm_password = $request->password;
            }
            $admin_user_exist->adm_updated_by = setCreatedUpdatedBy();
            $admin_user_exist->adm_updated_on = $current_date;
            if ($admin_user_exist->save()) {
                return redirect()->back()->with('success', 'Credentials updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Something went wrong.');
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }
}
