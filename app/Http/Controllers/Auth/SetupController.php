<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SetupController extends Controller
{
    /**
     * Show the form to create the very first (admin) account.
     * Locked out entirely once at least one user exists.
     */
    public function showSetupForm(): RedirectResponse|View
    {
        if (User::query()->exists()) {
            return redirect()->route('login');
        }

        return view('auth.setup');
    }

    /**
     * Create the first account, log the user in, and send them onward.
     */
    public function store(Request $request): RedirectResponse
    {
        // Re-check on submit too, in case someone else finished setup first
        // (double-tab, race condition, etc.).
        if (User::query()->exists()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'username'   => ['required', 'string', 'min:3', 'max:50'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'username'    => $validated['username'],
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'password'    => Hash::make($validated['password']),
            // First account on an empty system - give it the top role
            // since there'd otherwise be no way to promote it later.
            'user_level'  => 'superadmin',
            'first_login' => false,
            'revoked'     => false,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->event('setup')
            ->log('Initial admin account created');

        return redirect()->intended('controller');
    }
}