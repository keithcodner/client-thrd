<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * Handle an incoming admin authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Validate admin role requirements
        if (!$this->isAdminUser($user)) {
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'You do not have admin access. Please use the regular login.',
            ]);
        }

        $request->session()->regenerate();

        // Redirect to admin dashboard instead of regular dashboard
        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Check if the user meets admin requirements.
     */
    protected function isAdminUser($user): bool
    {
        // Check if user has role_id of 1 (admin role)
        if ($user->role_id != 1) {
            return false;
        }

        // Check if type is 'admin'
        if ($user->type !== 'admin') {
            return false;
        }

        // Check if name is either 'system' or 'admin'
        $validNames = ['system', 'admin'];
        if (!in_array(strtolower($user->name ?? ''), $validNames)) {
            return false;
        }

        return true;
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect to admin login page after logout
        return redirect()->route('admin.login.page');
    }
}
