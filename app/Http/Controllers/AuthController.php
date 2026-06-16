<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('authenticated')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password', '');

        if (!empty($username) || $password !== 'tieuhoa195') {
            return back()->withErrors(['login' => 'Thông tin đăng nhập không đúng.'])->withInput();
        }

        session(['authenticated' => true, 'user_name' => 'Sweetsica']);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        session()->forget(['authenticated', 'user_name']);

        return redirect()->route('login');
    }
}
