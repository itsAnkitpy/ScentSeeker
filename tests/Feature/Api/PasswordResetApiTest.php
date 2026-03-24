<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_reset_link_returns_success_for_existing_email(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'If your email exists in our system, you will receive a password reset link.');
    }

    public function test_send_reset_link_returns_success_for_nonexistent_email(): void
    {
        // Should return same message to prevent email enumeration
        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'If your email exists in our system, you will receive a password reset link.');
    }

    public function test_send_reset_link_requires_email(): void
    {
        $response = $this->postJson('/api/v1/forgot-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_send_reset_link_requires_valid_email(): void
    {
        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/v1/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Password reset successfully');

        // Verify password was actually changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_reset_password_with_invalid_token(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/reset-password', [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Invalid or expired token');
    }

    public function test_reset_password_requires_confirmation(): void
    {
        $response = $this->postJson('/api/v1/reset-password', [
            'token' => 'some-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_reset_password_requires_minimum_length(): void
    {
        $response = $this->postJson('/api/v1/reset-password', [
            'token' => 'some-token',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
