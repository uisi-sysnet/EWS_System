<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm(): RedirectResponse|View
    {
        return Auth::check()
            ? redirect()->intended('/dashboard')
            : view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'password' => ['required', 'string', 'min:5'],
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || $user->revoked) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $now = Carbon::now();
        
        if (is_null($user->login_at)) {
            $user->login_at = $now;
        }

        $user->last_login = Carbon::now();
        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at' => $user->login_at,
                'last_login' => $user->last_login,
            ])
            ->event('login')
            ->log('User logged in');


        if ($user->first_login) {
            return redirect()->route('password.change');
        }

        return redirect()->intended('controller');
    }

    public function logout(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user) {
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->event('logout')
                ->log('User logged out');
        }
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', __('auth.logout'));
    }
}
