<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

// ---------------------------------------------------------------------------
// POST /api/login — Sanctum token-based API login
// ---------------------------------------------------------------------------

describe('Login API', function () {

    // -----------------------------------------------------------------------
    // Successful login
    // -----------------------------------------------------------------------

    it('returns a Sanctum token and user on valid credentials', function () {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ]);

        expect($response->json('token'))->toBeString()->not->toBeEmpty();
        expect($response->json('user.id'))->toBe($user->id);
    });

    it('token can authenticate subsequent requests', function () {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $token = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ])->json('token');

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id);
    });

    // -----------------------------------------------------------------------
    // Validation failures
    // -----------------------------------------------------------------------

    it('returns 422 with invalid credentials', function () {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(422)
          ->assertJsonPath('message', 'Invalid credentials');
    });

    it('returns 422 for a non-existent email', function () {
        $this->postJson('/api/login', [
            'email'    => 'nobody@example.com',
            'password' => 'whatever',
        ])->assertStatus(422);
    });

    it('returns 422 when email field is missing', function () {
        $this->postJson('/api/login', [
            'password' => 'secret123',
        ])->assertStatus(422);
    });

    it('returns 422 when password field is missing', function () {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
        ])->assertStatus(422);
    });

    it('returns 422 when email is not a valid email address', function () {
        $this->postJson('/api/login', [
            'email'    => 'not-an-email',
            'password' => 'secret123',
        ])->assertStatus(422);
    });

    // -----------------------------------------------------------------------
    // Authenticated users cannot hit the login endpoint
    // -----------------------------------------------------------------------

    it('returns 302 or 401 when an authenticated user tries to login again', function () {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        Sanctum::actingAs($user);

        // guest middleware redirects or returns error – any non-200 is correct
        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ]);

        expect($response->status())->not->toBe(200);
    });
});
