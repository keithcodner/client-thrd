<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string'],
            'account_type' => ['nullable', 'string'],
            'business_name' => ['nullable', 'string'],
            'street_address' => ['nullable', 'string'],
            'hours' => ['nullable', 'string'],
            'capacity' => ['nullable', 'string'],
            'website' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string'],
            'tiktok' => ['nullable', 'string'],
            'primary_city' => ['nullable', 'string'],
            'photo' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'role_id' => 20, // Default role
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'phone_num' => $request->phone,
            'type' => $request->account_type,
            //'contact' => $request->business_name,
            //'history' => $request->street_address,
            //'about' => $request->hours,
            //'links' => $request->capacity,
            // 'website' => $request->website, // Column doesn't exist
            // 'instagram' => $request->instagram, // Use identity field instead if needed
            // 'tiktok' => $request->tiktok, // Column doesn't exist
            //'identity' => $request->instagram ?? 'anonymous',
            //'profile_photo_path' => $request->photo,
            'yourLocation' => $request->primary_city,
            'status' => 'active',
            'user_IsVerified' => 'no',
            'email_IsVerified' => 'no',
            'avatar' => 'users/avatar.png',
        ]);

        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            // Email event failed, but user was created - continue
            \Log::warning('Registration email event failed: ' . $e->getMessage());
        }

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }
}

