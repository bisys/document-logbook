<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role->slug;

            // Redirect based on role
            switch ($role) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'accounting-staff':
                    return redirect('/accounting/dashboard');
                case 'accounting-manager':
                    return redirect('/accounting-manager/dashboard');
                case 'accounting-gm':
                    return redirect('/accounting-gm/dashboard');
                case 'user':
                    return redirect('/user/dashboard');
                default:
                    Auth::logout();
                    return back()->with('error', 'Your account does not have a valid role. Please contact the administrator.');
            }

            return back()->with('error', 'Login failed. Please check your credentials.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
