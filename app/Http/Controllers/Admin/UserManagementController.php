<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->with('digitalIdentity')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function promote(User $user): RedirectResponse
    {
        $user->update(['is_admin' => true]);

        return to_route('admin.users.index')->with('status', "{$user->name} is now an administrator.");
    }

    public function demote(User $user): RedirectResponse
    {
        abort_if($user->is(Auth::user()), 403, 'You cannot demote your own account.');

        $user->update(['is_admin' => false]);

        return to_route('admin.users.index')->with('status', "{$user->name} is now a standard user.");
    }
}
