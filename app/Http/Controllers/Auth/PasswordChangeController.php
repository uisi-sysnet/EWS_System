<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{

    public function showChangeForm(): View
    {
        return view('auth.password-change');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password'     => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',      
                'regex:/[A-Z]/',      
                'regex:/[0-9]/',      
                'regex:/[@$!%*#?&]/', 
            ],
        ], [
            'new_password.min' => 'Password must be at least 8 characters long.',
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->password = Hash::make($request->new_password);
        $user->first_login = false;
        $user->save();

        return redirect()->intended('controller')
            ->with('status', 'Password changed successfully.');
    }
}
