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

            if ($role == 'admin') {
                return redirect('/admin/dashboard');
            }

            if ($role == 'accounting') {
                return redirect('/accounting/dashboard');
            }

            return redirect('user/dashboard');
        }

        return back()->with('error', 'Login failed. Please check your credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/login');
    }
}
