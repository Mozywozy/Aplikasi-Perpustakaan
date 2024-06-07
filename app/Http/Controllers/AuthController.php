<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function register()
    {
        return view('auth.register');
    }

    public function authenticating(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->status != 'active') {
                Alert::warning('Status', 'Your Account is not active yet. please contact admin!');
                return redirect('login');
            }
        }

        $request->session()->regenerate();
        if (Auth::check()) {
            if (Auth::user()->role_id == 1) {
                return redirect('admin.index_admin');
            } elseif (Auth::user()->role_id == 2) {
                return redirect('petugas');
            } elseif (Auth::user()->role_id == 3) {
                return redirect('customer');
            }
        } else {
            Alert::warning('Login Invalid!', 'please check your email & password!');
            return redirect('login');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function confirmLogout()
    {
        // Tampilkan Sweet Alert
        Alert::warning('Confirmation', 'Are you sure you want to logout?')
            ->showCancelButton(true)
            ->showConfirmButton('Yes, logout', '#3085d6')
            ->reverseButtons();
    }


    public function registerProses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:225',
            'password' => 'required|max:225',
            'email' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            Alert::warning('Error!', 'Invalid input! Please check back!');
            return redirect('register');
        }

        $role_id = 3;
        $status = 'active';
        // Hash password
        $hashedPassword = Hash::make($request->input('password'));

        // Buat pengguna dengan password yang telah di-hash
        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => $hashedPassword,
            'role_id' => $role_id,
            'status' => $status, // Tetapkan status sesuai dengan logika yang telah ditentukan
        ]);

        Alert::success('Register Success!', 'Please login again..');
        return redirect('register');
    }
}
