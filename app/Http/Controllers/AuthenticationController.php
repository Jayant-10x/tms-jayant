<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller {
    /**
     * Show login page
     */
    public function login() {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate user
     */
    public function authenticate(Request $request) {
        // 1. Validate the incoming request data
        $credentials = $request->validate([
            'adm_user_name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
//        $remember = $request->boolean('remember');
        // 2. Attempt to log the user in using the credentials
        if (Auth::attempt($credentials)) {
            $admin = Auth::user();

            // 3. Check if the authenticated administrator is active
            if ($admin->adm_status == 1) {
                $request->session()->regenerate();

                $logged_in_user_session_arr = [
                    'user_id'   => $admin->adm_id,
                    'emp_id'    => $admin->adm_emp_id,
                    'user_name' => $admin->adm_user_name,
                    'user_role' => $admin->adm_role,
                ];

                $request->session()->put('logged_in_user_session', $logged_in_user_session_arr);

                return redirect()->intended(route('dashboard'))->with('success', 'Login successful.');
            }

            // 4. If status is deactivated, log them out immediately and throw validation error
            Auth::logout();

            throw ValidationException::withMessages([
                'adm_user_name' => 'Your profile is deactivated. Please contact the administrator.',
            ]);
        }

        // 5. If authentication fails entirely, throw standard validation error
        throw ValidationException::withMessages([
            'adm_user_name' => 'The username or password is incorrect.',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->forget('logged_in_user_session');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}
