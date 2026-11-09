<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle a login attempt.
     *
     * Authenticates using username (not email) and redirects
     * based on the user's role: kasir, gudang, or admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            $role = Auth::user()->role;

            return match ($role) {
                'kasir'  => redirect('/kasir'),
                'gudang' => redirect('/gudang'),
                'admin'  => redirect('/admin'),
                default  => redirect('/login')->withErrors(['username' => 'Role tidak dikenali.']),
            };
        }

        return back()->withErrors([
            'username' => 'Username atau kata sandi salah.',
        ])->onlyInput('username');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
