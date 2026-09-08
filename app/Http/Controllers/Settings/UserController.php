<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->where('revoked', false)
            ->select([
                'id',
                'first_name',
                'last_name',
                'position',
                'username',
                'email',
                'contact_number',
                'user_level',
                'active',
                'created_at',
            ])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('settings.users', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:80'],
            'last_name'      => ['required', 'string', 'max:80'],
            'position'       => ['required', 'string', 'max:100'],
            'user_level'     => ['required', 'in:superadmin,admin,user,viewer'],
            'email'          => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'contact_number' => ['nullable', 'string', 'max:20'],
        ]);

        $first = mb_strtolower(trim($validated['first_name']), 'UTF-8');
        $last  = mb_strtolower(trim($validated['last_name']),  'UTF-8');

        $base  = $first[0] . '.' . $last;
        $username = $base;
        $counter  = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter++;
        }

        $year = date('Y');
        $plainPassword = $username . $year . '!!';

        User::create([
            'first_name'     => $validated['first_name'],
            'last_name'      => $validated['last_name'],
            'username'       => $username,
            'password'       => Hash::make($plainPassword),
            'position'       => $validated['position'],
            'user_level'     => $validated['user_level'],
            'email'          => $request->filled('email') ? $validated['email'] : null,
            'contact_number' => $request->filled('contact_number') ? $validated['contact_number'] : null,
            'active'         => true,
        ]);

        return redirect()
            ->route('settings.users')
            ->with([
                'status'        => 'success',
                'message'       => "User created successfully!",
                'username'      => $username,
                'temp_password' => $plainPassword,
            ]);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($id);

        Log::info('User update initiated', [
            'user_id' => $user->id,
            'request_data' => $request->except(['_token', '_method']),
            'has_active' => $request->has('active'),
            'wants_json' => $request->wantsJson()
        ]);

        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:80'],
            'last_name'      => ['required', 'string', 'max:80'],
            'position'       => ['required', 'string', 'max:100'],
            'user_level'     => ['required', 'in:superadmin,admin,user,viewer'],
            'email'          => ['nullable', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'active'         => ['sometimes', 'boolean'],
        ]);

        Log::info('Validated data', $validated);

        if (empty($validated['email'])) {
            $validated['email'] = null;
        }
        if (empty($validated['contact_number'])) {
            $validated['contact_number'] = null;
        }

        $validated['active'] = $request->has('active');

        $user->update($validated);
        $user->refresh();

        Log::info('After update', $user->toArray());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!'
            ]);
        }

        return redirect()
            ->route('settings.users')
            ->with([
                'status'  => 'success',
                'message' => 'User updated successfully!',
            ]);
    }

    public function destroy(Request $request, User $user): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($user->id === Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot revoke your own account.'
                ], 403);
            }
            return redirect()->back()->with('error', 'You cannot revoke your own account.');
        }

        $user->revoked = true;
        $user->save();

        Log::info('User revoked', [
            'revoked_user_id' => $user->id,
            'revoked_by' => Auth::id()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User revoked successfully.'
            ]);
        }

        return redirect()->route('settings.users')->with('success', 'User revoked successfully.');
    }
}
