<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\OrangTua;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        // Remove this line as middleware() method is not available in Laravel 12 controllers
        // $this->middleware('guest:admin,guru,siswa,orangtua')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function showGuruLoginForm()
    {
        return view('auth.guru.login');
    }
    
    public function showSiswaLoginForm()
    {
        return view('auth.siswa.login');
    }
    
    public function showOrangtuaLoginForm()
    {
        return view('auth.orangtua.login');
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin.login');
    }

    /**
     * Generic login method that attempts all user types.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = $request->input('login');
        $password = $request->input('password');

        // Try to authenticate as admin
        if (Auth::guard('admin')->attempt(['username' => $loginField, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Try to authenticate as guru
        if (Auth::guard('guru')->attempt(['username' => $loginField, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('guru.dashboard'));
        }

        // Try to authenticate as siswa
        if (Auth::guard('siswa')->attempt(['nisn' => $loginField, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('siswa.dashboard'));
        }        // Try to authenticate as orangtua (via siswa's nisn)
        $siswa = Siswa::where('nisn', $loginField)->first();
        if ($siswa && $siswa->orangtua) {
            $orangtua = $siswa->orangtua;
            if (Auth::guard('orangtua')->attempt(['id' => $orangtua->id, 'password' => $password], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('orangtua.dashboard'));
            }
        }

        // Authentication failed
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('guru')->logout();
        Auth::guard('siswa')->logout();
        Auth::guard('orangtua')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Handle admin login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt admin authentication
        if (Auth::guard('admin')->attempt([
            'username' => $request->username,
            'password' => $request->password
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            // Redirect to admin dashboard specifically
            return redirect()->intended(route('admin.dashboard'));
        }

        // Authentication failed
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    /**
     * Handle guru login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guruLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('guru')->attempt([
            'username' => $request->username,
            'password' => $request->password
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('guru.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    /**
     * Handle siswa login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function siswaLogin(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('siswa')->attempt([
            'nisn' => $request->nisn,
            'password' => $request->password
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('siswa.dashboard'));
        }

        return back()->withErrors([
            'nisn' => 'The provided credentials do not match our records.',
        ])->onlyInput('nisn');
    }

    /**
     * Handle orangtua login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function orangtuaLogin(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'password' => 'required|string',
        ]);        // For orangtua, we need to find the associated siswa first
        $siswa = Siswa::where('nisn', $request->nisn)->first();
        
        if ($siswa && $siswa->orangtua) {
            $orangtua = $siswa->orangtua;
            
            if (Auth::guard('orangtua')->attempt([
                'id' => $orangtua->id, 
                'password' => $request->password
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('orangtua.dashboard'));
            }
        }

        return back()->withErrors([
            'nisn' => 'The provided credentials do not match our records.',
        ])->onlyInput('nisn');
    }

    /**
     * Handle admin logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Handle guru logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guruLogout(Request $request)
    {
        Auth::guard('guru')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Handle siswa logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function siswaLogout(Request $request)
    {
        Auth::guard('siswa')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Handle orangtua logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function orangtuaLogout(Request $request)
    {
        Auth::guard('orangtua')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
