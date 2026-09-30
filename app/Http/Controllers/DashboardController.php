<?php

namespace App\Http\Controllers;

class DashboardController extends Controller {
    public function index() {
        $user_statistics = [];
        if (!is_admin()) {
            $user_statistics = (new ProjectTaskController())->userTaskStatistics(get_logged_in_user_emp_id());
        }
        return view('dashboard', compact('user_statistics'));
    }
}
