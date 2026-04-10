<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $role = Auth::user()->role->slug;

            // Redirect based on role
            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'accounting-staff':
                    return redirect()->route('accounting-staff.dashboard');
                case 'accounting-manager':
                    return redirect()->route('accounting-manager.dashboard');
                case 'accounting-gm':
                    return redirect()->route('accounting-gm.dashboard');
                case 'user':
                    return redirect()->route('user.dashboard');
                default:
                    Auth::logout();
                    return back()->with('error', 'Your account does not have a valid role. Please contact the administrator.');
            }
        } else {
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

    public function changePasswordForm()
    {
        return view('auth.change-password');
    }  

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password does not match.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Password changed successfully. Please login again.');
    }
}
